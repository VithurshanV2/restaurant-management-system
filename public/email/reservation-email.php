<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../../vendor/autoload.php";
function sendReservationEmail($customerEmail, $customerName, $reservationDate, $reservationTime, $action)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "example@gmail.com";
        $mail->Password = "app_password_from_gmail";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom("example@gmail.com", "Restaurant Name");
        $mail->addAddress($customerEmail, $customerName);

        $mail->isHTML(true);
        if ($action == "confirm") {
            $mail->Subject = "Reservation Confirmed";
            $mail->Body = "<p>Dear $customerName,</p>
                <p>Your reservation for <strong>$reservationDate</strong> at <strong>$reservationTime</strong> has been confirmed.</p>
                <p>Thank you for the reservation. We will be waiting for your arrival.</p>";
        } elseif ($action == "cancel") {
            $mail->Subject = "Reservation Canceled";
            $mail->Body = "<p>Dear $customerName,</p>
                <p>We regret to inform you that your reservation for <strong>$reservationDate</strong> at <strong>$reservationTime</strong> has been canceled.</p>
                <p>We apologize for the cancellation, no vacancies currently available for the requested reservation.</p>";
        }

        $mail->AltBody = strip_tags($mail->Body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send email: {$mail->ErrorInfo}");
        return false;
    }
}
