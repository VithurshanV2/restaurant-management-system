<?php
session_start();
require "../../config/db-connection.php";

$errors = [];
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = trim($_POST["reservation_id"]);
    $action = trim($_POST["action"]);
    $date = trim($_POST["date"]);
    $form_data = $_POST;

    if (empty($reservation_id) || !is_numeric($reservation_id)) {
        $errors["reservation_id_error"] = "Invalid reservation ID";
    }

    if (empty($action)) {
        $errors["action_error"] = "Action is required";
    } elseif ($action != "confirm" && $action != "cancel") {
        $errors["action_error"] = "Invalid action";
    }

    if (empty($errors)) {
        if ($action == "confirm") {
            $stmt = $conn->prepare("UPDATE reservations SET status = 'confirmed' WHERE reservation_id = ?");
            $stmt->bind_param("i", $reservation_id);

            if ($stmt->execute()) {
                $_SESSION["success_message"] = "Reservation confirmed successfully";
            } else {
                $errors["confirm_error"] = "Reservation was not confirmed";
            }
        } elseif ($action == "cancel") {
            $stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
            $stmt->bind_param("i", $reservation_id);

            if ($stmt->execute()) {
                $_SESSION["success_message"] = "Reservation cancelled successfully";
            } else {
                $errors["cancel_error"] = "Reservation was not cancelled";
            }
        }
    }
    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        $_SESSION['form_data'] = $form_data;
    }

    header("Location: manage-reservation-front.php");
    exit();
}

$conn->close();
