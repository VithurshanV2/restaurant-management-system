<?php
session_start();
require "../../config/db-connection.php";

$errors = [];
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_shift"])) {
        $employee_id = trim($_POST["employee_id"]);
        $shift_date = trim($_POST["shift_date"]);
        $shift_start = trim($_POST["shift_start"]);
        $shift_end = trim($_POST["shift_end"]);
        $form_data = $_POST;

        if (empty($employee_id)) {
            $errors["employee_error"] = "Please select an employee";
        }
        if (empty($shift_date)) {
            $errors["shift_date_error"] = "Shift date is required";
        }
        if (empty($shift_start) || empty($shift_end)) {
            $errors["shift_time_error"] = "Both start and end time are required";
        } elseif ($shift_start >= $shift_end) {
            $errors["shift_time_error"] = "End time must be after start time";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO shifts (employee_id, shift_date, shift_start, shift_end) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $employee_id, $shift_date, $shift_start, $shift_end);
            $stmt->execute();
            $_SESSION["success_message"] = "Shift added successfully";
        } else {
            $_SESSION["errors"] = $errors;
            $_SESSION["form_data"] = $form_data;
        }
        header("Location: manage-shifts-front.php");
        exit();
    }

    if (isset($_POST["remove_shift"])) {
        $shift_id = trim($_POST["shift_id"]);
        $form_data = $_POST;

        if (empty($shift_id) || !is_numeric($shift_id)) {
            $errors["remove_shift_error"] = "Invalid shift ID";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("DELETE FROM shifts WHERE shift_id = ?");
            $stmt->bind_param("i", $shift_id);
            if ($stmt->execute()) {
                $_SESSION["success_message"] = "Shift removed successfully";
            } else {
                $errors["remove_shift_error"] = "Error removing shift";
            }
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            $_SESSION["form_data"] = $form_data;
        }
        header("Location: manage-shifts-front.php");
        exit();
    }
}

$conn->close();
