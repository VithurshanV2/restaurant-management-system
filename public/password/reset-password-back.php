<?php
session_start();
require "../../config/db-connection.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = htmlspecialchars(trim($_POST["token"]));
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $password_regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/";

    if (empty($password)) {
        $errors["password_error"] = "Password is required";
    }
    if (!preg_match($password_regex, $password)) {
        $errors["password_error"] = "Password must be at least 8 characters, and contain at least one uppercase & lowercase letter, and one number";
    }
    if ($password !== $confirm_password) {
        $errors["confirm_password_error"] = "Passwords do not match";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id, user_type, expires_at FROM password_reset WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $user_type, $expires_at);
            $stmt->fetch();

            if (strtotime($expires_at) > time()) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                if ($user_type === "customer") {
                    $stmt = $conn->prepare("UPDATE customers SET password = ? WHERE id = ?");
                } else {
                    $stmt = $conn->prepare("UPDATE employees SET password = ? WHERE id = ?");
                }

                $stmt->bind_param("si", $hashed_password, $user_id);
                $stmt->execute();

                $stmt = $conn->prepare("DELETE FROM password_reset WHERE token = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();

                $_SESSION["success_message"] = "Password reset successfully. Please log in.";
                header("Location: ../login/login-front.php");
                exit();
            } else {
                $errors["token_error"] = "Reset token has expired";
            }
        } else {
            $errors["token_error"] = "Invalid token";
        }
        $stmt->close();
    }
}

if (!empty($errors)) {
    $_SESSION["errors"] = $errors;
    header("Location: reset-password-front.php?token=" . urlencode($token));
    exit();
}
