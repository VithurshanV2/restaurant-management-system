<?php
session_start();
require "../../config/db-connection.php";

$errors = [];
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $day = trim($_POST["days_in_week"]);
    $opening_time = trim($_POST["opening_time"]);
    $closing_time = trim($_POST["closing_time"]);
    $form_data = $_POST;

    if (empty($day)) {
        $errors["day_error"] = "Please select a day of the week";
    }
    if (empty($opening_time)) {
        $errors["opening_time_error"] = "Please select an opening time";
    }
    if (empty($closing_time)) {
        $errors["closing_time_error"] = "Please select a closing time";
    }
    if ($opening_time >= $closing_time) {
        $errors["time_error"] = "Closing time must be after opening time";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM restaurant_hours WHERE days_in_week = ?");
        $stmt->bind_param("s", $day);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE restaurant_hours SET opening_time = ?, closing_time = ? WHERE days_in_week = ?");
            $stmt->bind_param("sss", $opening_time, $closing_time, $day);

            if ($stmt->execute()) {
                $_SESSION["success_message"] = "updated successfully";
                unset($_SESSION["form_data"], $_SESSION["errors"]);
            } else {
                $errors["update_error"] = "An error occurred while updating the changes";
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO restaurant_hours (days_in_week, opening_time, closing_time) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $day, $opening_time, $closing_time);
            if ($stmt->execute()) {
                $_SESSION["success_message"] = "Added successfully";
                unset($_SESSION["form_data"], $_SESSION["errors"]);
            } else {
                $errors["insert_error"] = "An error occurred while adding the new record";
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        $_SESSION["form_data"] = $form_data;
        header("Location: manage-time-front.php");
        exit();
    }

    header("Location: manage-time-front.php");
    exit();
}

$conn->close();
