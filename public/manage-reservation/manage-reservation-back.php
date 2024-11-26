<?php
require "../../includes/session.php";
require "../../config/db-connection.php";
require "../email/reservation-email.php";

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
        $stmt = $conn->prepare("SELECT customers.email, reservations.customer_name, reservations.reservation_date, reservations.reservation_time 
        FROM reservations 
        JOIN customers ON reservations.customer_id = customers.id 
        WHERE reservations.reservation_id = ?");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $reservation = $result->fetch_assoc();
            $customer_email = $reservation["email"];
            $customer_name = $reservation["customer_name"];
            $reservation_date = $reservation["reservation_date"];
            $reservation_time = $reservation["reservation_time"];

            if ($action == "confirm") {
                $stmt = $conn->prepare("UPDATE reservations SET status = 'confirmed' WHERE reservation_id = ?");
                $stmt->bind_param("i", $reservation_id);

                if ($stmt->execute()) {
                    if (sendReservationEmail($customer_email, $customer_name, $reservation_date, $reservation_time, "confirm")) {
                        $_SESSION["success_message"] = "Reservation confirmed and email sent successfully";
                    } else {
                        $_SESSION["success_message"] = "Reservation confirmed but email failed to send";
                    }
                } else {
                    $errors["confirm_error"] = "Reservation was not confirmed";
                }
            } elseif ($action == "cancel") {
                $stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
                $stmt->bind_param("i", $reservation_id);

                if ($stmt->execute()) {
                    if (sendReservationEmail($customer_email, $customer_name, $reservation_date, $reservation_time, "cancel")) {
                        $_SESSION["success_message"] = "Reservation canceled and email sent successfully";
                    } else {
                        $_SESSION["success_message"] = "Reservation canceled but email failed to send";
                    }
                } else {
                    $errors["cancel_error"] = "Reservation was not canceled";
                }
            }
        } else {
            $errors["reservation_error"] = "Reservation not found";
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
