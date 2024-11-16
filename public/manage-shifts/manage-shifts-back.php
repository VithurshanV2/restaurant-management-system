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
            $stmt = $conn->prepare("SELECT * FROM employees WHERE employee_id = ?");
            $stmt->bind_param("s", $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                $errors["employee_error"] = "Selected employee does not exist";
            } else {
                if (empty($errors)) {
                    $stmt = $conn->prepare("INSERT INTO shifts (employee_id, shift_date, shift_start, shift_end) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $employee_id, $shift_date, $shift_start, $shift_end);
                    if ($stmt->execute()) {
                        $_SESSION["success_message"] = "Shift added successfully";
                    } else {
                        $errors["add_shift_error"] = "Shift not added";
                    }
                }
            }
        }

        if (!empty($errors)) {
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
                $errors["remove_shift_error"] = "Shift not removed";
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

if (isset($_POST["update_shift"])) {
    $shift_id = trim($_POST["shift_id"]);
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
        $stmt = $conn->prepare("UPDATE shifts SET employee_id = ?, shift_date = ?, shift_start = ?, shift_end = ? WHERE shift_id = ?");
        $stmt->bind_param("ssssi", $employee_id, $shift_date, $shift_start, $shift_end, $shift_id);
        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Shift updated successfully";
        } else {
            $errors["update_shift_error"] = "Shift not updated";
        }
    }

    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        $_SESSION["form_data"] = $form_data;
    }
    header("Location: manage-shifts-front.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["duplicate_shifts"])) {
        $duplicate_date = trim($_POST["duplicate_date"]);
        $target_date = trim($_POST["target_date"]);

        if (empty($duplicate_date)) {
            $errors["duplicate_date_error"] = "Please select a date to duplicate from";
        }

        if (empty($target_date)) {
            $errors["target_date_error"] = "Please select a target date";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT * FROM shifts WHERE shift_date = ?");
            $stmt->bind_param("s", $duplicate_date);
            $stmt->execute();
            $shifts = $stmt->get_result();

            if ($shifts->num_rows > 0) {
                while ($shift = $shifts->fetch_assoc()) {
                    $stmt = $conn->prepare("INSERT INTO shifts (employee_id, shift_date, shift_start, shift_end) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $shift['employee_id'], $target_date, $shift['shift_start'], $shift['shift_end']);
                    $stmt->execute();
                }
                $_SESSION["success_message"] = "Shifts duplicated successfully";
            } else {
                $errors["duplicate_error"] = "No shifts found for the selected date";
            }
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
        }

        header("Location: manage-shifts-front.php");
        exit();
    }
}

$conn->close();
