<?php
require "../../includes/session.php";
require "../../config/db-connection.php";

$errors = [];
$form_data = [];

if (isset($_POST["remove_reservation"])) {
    $reservation_id = trim($_POST["remove_reservation_id"]);
    $form_data = $_POST;

    $current_date = isset($_POST["date"]) ? $_POST["date"] : (isset($_SESSION["form_data"]["date"]) ? $_SESSION["form_data"]["date"] : date('Y-m-d'));

    if (empty($reservation_id) || !is_numeric($reservation_id)) {
        $errors["remove_reservation_error"] = "Invalid reservation ID";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = ?");
        $stmt->bind_param("i", $reservation_id);
        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Reservation removed successfully";
        } else {
            $errors["remove_reservation_error"] = "Reservation was not removed";
        }
    }

    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        $_SESSION["form_data"] = $form_data;
    }

    $_SESSION["form_data"]["date"] = $current_date;
    header("Location: manage-reservation-front.php?date=" . urlencode($current_date));
    exit();
}

$conn->close();
