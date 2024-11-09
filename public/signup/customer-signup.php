<?php
session_start();
include "./config/db-connection.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    
    if (empty($username)) {
        $errors["username_error"] = "Username is required";
    }
    if (empty($email)) {
        $errors["email_error"] = "Email is required";
    }if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
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

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM customers WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors["username_error"] = "Username is already taken";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO customers (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Registration successful";
            header("Location: login.php");
            exit();
        } else {
            $errors["error_execution"] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: customer-signup.html");
        exit();
    }
}

$conn->close();
?>
