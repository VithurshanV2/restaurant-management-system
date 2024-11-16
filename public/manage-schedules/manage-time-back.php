<?php
session_start();
require "../../config/db-connection.php";

$errors = [];
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_schedule'])) {
    $day = trim($_POST["edit_day"]);
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
        $errors["time_error"] = "Closing time must be after opening time, Please try again";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE restaurant_hours SET opening_time = ?, closing_time = ? WHERE days_in_week = ?");
        $stmt->bind_param("sss", $opening_time, $closing_time, $day);

        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Updated successfully";
        } else {
            $errors["update_error"] = "An error occurred while updating the changes";
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_close_day'])) {
    $day = trim($_POST["day"]);
    $status = intval($_POST["status"]);

    if ($status == 1) {
        $stmt = $conn->prepare("UPDATE restaurant_hours SET is_closed = 1 WHERE days_in_week = ?");
        $stmt->bind_param("s", $day);

        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Day closed successfully";
        } else {
            $_SESSION["errors"][] = "Failed to close day";
        }
    } else {
        $stmt = $conn->prepare("UPDATE restaurant_hours SET is_closed = 0 WHERE days_in_week = ?");
        $stmt->bind_param("s", $day);

        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Day reopened successfully";
        } else {
            $_SESSION["errors"][] = "Failed to reopen day";
        }
    }

    if (!empty($_SESSION["errors"])) {
        $_SESSION["form_data"] = $_POST;
    }

    header("Location: manage-time-front.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_closed_date"])) {
        $date = trim($_POST["date"]);
        $reason = !empty(trim($_POST["reason"])) ? trim($_POST["reason"]) : 'Not specified';
        $form_data = $_POST;

        if (empty($date)) {
            $errors["date_error"] = "Closed date is required";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO closed_dates (closed_date, reason) VALUES (?, ?)");
            $stmt->bind_param("ss", $date, $reason);
            $stmt->execute();
            $_SESSION["success_message"] = "Closed date added successfully";
        } else {
            $_SESSION["errors"] = $errors;
            $_SESSION["form_data"] = $form_data;
        }
        header("Location: manage-time-front.php");
        exit();
    }
}

if (isset($_POST["remove_closed_date"])) {
    $closed_date_id = trim($_POST["remove_closed_date_id"]);
    $form_data = $_POST;

    if (empty($closed_date_id) || !is_numeric($closed_date_id)) {
        $errors["remove_closed_date_id_error"] = "Invalid closed date ID";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("DELETE FROM closed_dates WHERE id = ?");
        $stmt->bind_param("i", $closed_date_id);
        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Closed date removed successfully";
        } else {
            $errors["remove_closed_date_error"] = "Error removing closed date";
        }
    }

    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        $_SESSION["form_data"] = $form_data;
    }

    header("Location: manage-time-front.php");
    exit();
}

$conn->close();
