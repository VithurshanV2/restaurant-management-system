<?php
session_start();
require "../../config/db-connection.php";
require "../email/reset-password-email.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        $errors["email_error"] = "Email is required";
    } else {
        $user_type = null;
        $user_id = null;
        $username = null;

        $stmt = $conn->prepare("SELECT id, username FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $username);
            $stmt->fetch();
            $user_type = "customer";
        } else {
            $stmt = $conn->prepare("SELECT id, username FROM employees WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($user_id, $username);
                $stmt->fetch();
                $user_type = "employee";
            }
        }

        $stmt->close();

        if ($user_type) {
            $token = bin2hex(random_bytes(32));
            $expires_at = date("Y-m-d H:i:s", time() + 30 * 60);

            $stmt = $conn->prepare("INSERT INTO password_reset (user_id, user_type, token, expires_at) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $user_type, $token, $expires_at);
            $stmt->execute();

            $reset_link = "http://restaurant-management-system.localhost/public/password/reset-password-front.php?token=$token";

            if (sendPasswordResetEmail($email, $username, $reset_link)) {
                $_SESSION["success_message"] = "Password reset link sent to your email";
                header("Location: ../login/login-front.php");
                exit();
            } else {
                $errors["email_error"] = "Failed to send email. Try again later";
            }
        } else {
            $errors["email_error"] = "Email not found";
        }
    }
}

if (!empty($errors)) {
    $_SESSION["errors"] = $errors;
    header("Location: forgot-password-front.php");
    exit();
}
