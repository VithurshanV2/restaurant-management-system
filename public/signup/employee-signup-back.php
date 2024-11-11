<?php
session_start();
require "../../config/db-connection.php";

$errors = [];
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $job_role = trim($_POST["job_role"]);
    $employee_id = trim($_POST["employee_id"]);
    $contact_number = trim($_POST["contact_number"]);
    $form_data = $_POST;

    if (empty($username)) {
        $errors["username_error"] = "Username is required";
    }
    if (empty($first_name)) {
        $errors["first_name_error"] = "First name is required";
    }
    if (empty($last_name)) {
        $errors["last_name_error"] = "Last name is required";
    }
    if (empty($email)) {
        $errors["email_error"] = "Email is required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email_error"] = "Invalid email address";
    }
    if (empty($password)) {
        $errors["password_error"] = "Password is required";
    }
    $password_regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/";
    if (!preg_match($password_regex, $password)) {
        $errors["password_error"] = "Password must be at least 8 characters, and contain at least one uppercase & lowercase letter, and one number";
    }
    if ($password !== $confirm_password) {
        $errors["confirm_password_error"] = "Passwords do not match";
    }
    if (empty($employee_id)) {
        $errors["employee_id_error"] = "Employee ID is required";
    }
    if (empty($contact_number)) {
        $errors["contact_number_error"] = "Contact number is required";
    } elseif (!preg_match('/^\d{10}$/', $contact_number)) {
        $errors["contact_number_error"] = "Please enter a valid contact number";
    }


    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM employees WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors["username_error"] = "Username is already taken";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM employees WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors["email_error"] = "Email is already taken";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO employees (username, email, password, first_name, last_name, job_role, employee_id, contact_number, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssssss", $username, $email, $hashed_password, $first_name, $last_name, $job_role, $employee_id, $contact_number);

        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Registration successful";
            unset($_SESSION["form_data"]);
            unset($_SESSION["errors"]);
            header("Location: login.php");
            exit();
        } else {
            $errors["error_execution"] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $form_data;
        header("Location: employee-signup-front.php");
        exit();
    }
}

$conn->close();
