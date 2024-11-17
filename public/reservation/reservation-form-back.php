<?php
session_start();
require "../../config/db-connection.php";

$errors = [];
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_date = trim($_POST["reservation_date"]);
    $reservation_time = trim($_POST["reservation_time"]);
    $party_size = trim($_POST["party_size"]);
    $customer_name = trim($_POST["customer_name"]);
    $notes = !empty(trim($_POST["notes"])) ? trim($_POST["notes"]) : 'Not specified';
    $day_of_week = date("l", strtotime($reservation_date));
    $email = trim($_POST["email"]);

    $form_data = $_POST;

    if (!isset($_SESSION["customer_id"])) {
        header("Location: ../login/login-front.php");
        exit;
    }

    $customer_id = $_SESSION["customer_id"];

    $stmt = $conn->prepare("SELECT * FROM closed_dates WHERE closed_date = ?");
    $stmt->bind_param("s", $reservation_date);
    $stmt->execute();
    $closed_result = $stmt->get_result();

    if ($closed_result->num_rows > 0) {
        $errors["date_error"] = "The restaurant is closed on this day";
    }

    $stmt = $conn->prepare("SELECT opening_time, closing_time FROM restaurant_hours WHERE days_in_week = ? AND is_closed = 0");
    $stmt->bind_param("s", $day_of_week);
    $stmt->execute();
    $hours_result = $stmt->get_result();

    if ($hours_result->num_rows > 0) {
        $hours = $hours_result->fetch_assoc();
        $opening_time = $hours["opening_time"];
        $closing_time = $hours["closing_time"];

        if ($reservation_time < $opening_time || $reservation_time >= $closing_time) {
            $errors["time_error"] = "The restaurant is not open at this time";
        }
    } else {
        $errors["date_error"] = "The restaurant is closed on this day";
    }

    if (empty($party_size) || !is_numeric($party_size)) {
        $errors["party_size_error"] = "Please enter a valid party size";
    }

    if (empty($errors)) {
        $status = "pending";
        $stmt = $conn->prepare("INSERT INTO reservations (customer_id, customer_name, reservation_date, reservation_time, party_size, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdsss", $customer_id, $customer_name, $reservation_date, $reservation_time, $party_size, $status, $notes);
        $stmt->execute();
        $reservation_id = $stmt->insert_id;

        $_SESSION["success_message"] = "Reservation requested. Please wait for confirmation";
        header("Location: reservation-confirmation-front.php");
        exit;
    } else {
        $_SESSION["errors"] = $errors;
        $_SESSION["form_data"] = $form_data;
        header("Location: reservation-form-front.php");
        exit;
    }
}

$conn->close();
