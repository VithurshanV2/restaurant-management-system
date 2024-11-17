<?php
session_start();
require "../../config/db-connection.php";

$errors = [];
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = trim($_POST["username_email"]);
    $password = trim($_POST["password"]);
    $form_data = $_POST;

    if (empty($username_email)) {
        $errors["username_email_error"] = "Username/Email is required";
    }
    if (empty($password)) {
        $errors["password_error"] = "Password is required";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, password FROM customers WHERE (username = ? OR email = ?)");
        $stmt->bind_param("ss", $username_email, $username_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user, $hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION["customer_id"] = $user;
                $_SESSION["user_type"] = "customer";
                header("Location: ../home/home.html");
                exit();
            } else {
                $errors["password_error"] = "Incorrect password";
            }
        } else {
            $stmt->close();

            $stmt = $conn->prepare("SELECT id, password FROM employees WHERE (username = ? OR email = ?)");
            $stmt->bind_param("ss", $username_email, $username_email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($user, $hashed_password);
                $stmt->fetch();
                if (password_verify($password, $hashed_password)) {
                    $_SESSION["employee_id"] = $user;
                    $_SESSION["user_type"] = "employee";
                    header("Location: ../employee-shifts/employee-shifts-front.php");
                    exit();
                } else {
                    $errors["password_error"] = "Incorrect password";
                }
            } else {
                $errors["username_email_error"] = "Invalid username or email";
            }
        }
        $stmt->close();
    }
    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        $_SESSION['form_data'] = $form_data;
        header("Location: login-front.php");
        exit();
    }
}
