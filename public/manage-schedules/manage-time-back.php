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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_time_slot"])) {
        $day = trim($_POST["day"]);
        $start_time = trim($_POST["start_time"]);
        $end_time = trim($_POST["end_time"]);
        $form_data = $_POST;

        if (empty($day)) {
            $errors["day_error"] = "Please select a day";
        }
        if (empty($start_time)) {
            $errors["start_time_error"] = "Start time is required";
        }
        if (empty($end_time)) {
            $errors["end_time_error"] = "End time is required";
        }
        if ($start_time >= $end_time) {
            $errors["time_error"] = "End time must be after start time, Try again";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT * FROM time_slots WHERE day = ? AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?))");
            $stmt->bind_param("sssss", $day, $end_time, $start_time, $start_time, $end_time);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $errors["duplicate_error"] = "This time slot overlaps with an existing one, Try again";
            }
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO time_slots (day, start_time, end_time) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $day, $start_time, $end_time);

            if ($stmt->execute()) {
                $_SESSION["success_message"] = "Time slot added successfully";
            } else {
                $errors["add_error"] = "Failed to add time slot";
            }
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            $_SESSION["form_data"] = $form_data;
        }

        header("Location: manage-time-front.php");
        exit();
    }

    if (isset($_POST["remove_time_slot"])) {
        $slot_id = intval($_POST["slot_id"]);

        if (empty($slot_id) || !is_numeric($slot_id)) {
            $errors["slot_id_error"] = "Invalid time slot ID";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("DELETE FROM time_slots WHERE id = ?");
            $stmt->bind_param("i", $slot_id);

            if ($stmt->execute()) {
                $_SESSION["success_message"] = "Time slot removed successfully";
            } else {
                $errors["remove_error"] = "Failed to remove time slot";
            }
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
        }

        header("Location: manage-time-front.php");
        exit();
    }
}

$conn->close();
