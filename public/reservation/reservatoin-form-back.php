<?php
session_start();
require "../../config/db_connection.php";

$errors = [];
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_date = trim($_POST["date"]);
    $reservation_time = trim($_POST["time"]);
    $party_size = trim($_POST["party_size"]);
    $form_data = $_POST;

    if (empty($reservation_date)) {
        $errors["date_error"] = "Please select a date";
    }
    if (empty($reservation_time)) {
        $errors["time_error"] = "Please select a time";
    }
    if (empty($party_size) || !is_numeric($party_size)) {
        $errors["party_size_error"] = "Please enter a valid party size";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT table_id, seat_count FROM tables WHERE available = 1 AND seat_count >= ?");
        $stmt->bind_param("i", $party_size);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $customer_id = 1;

            $stmt = $conn->prepare("INSERT INTO reservations (customer_id, reservation_date, reservation_time, party_size, status) VALUES (?,?,?,?, 'pending')");
            $stmt->bind_param("isss", $customer_id, $reservation_date, $reservation_time, $party_size);
            $stmt->execute();
            $reservation_id = $stmt->insert_id;

            while ($row = $result->fetch_assoc()) {
                $table_id = $row["table_id"];

                $stmt = $conn->prepare("INSERT INTO reservation_tables (reservation_id, table_id) VALUES (?,?)");
                $stmt->bind_param("ii", $reservation_id, $table_id);
                $stmt->execute();

                $stmt = $conn->prepare("UPDATE tables SET available = 0 WHERE table_id = ?");
                $stmt->bind_param("i", $table_id);
                $stmt->execute();
            }
            $_SESSION["success_message"] = "Reservation created successfully";
            unset($_SESSION["form_data"], $_SESSION["errors"]);
            $_SESSION['reservation_id'] = $reservation_id;
            header("Location: checkout.php");
            exit();
        } else {
            $errors["availability_error"] = "No tables are currently available for the selected time and party size";
        }
    }
    $_SESSION["errors"] = $errors;
    $_SESSION["form_data"] = $form_data;
    header("Location: reservation-form.php");
    exit();
}

$conn->close();
