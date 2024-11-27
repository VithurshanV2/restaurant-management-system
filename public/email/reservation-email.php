<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../../vendor/autoload.php";

function sendReservationEmail($customer_email, $customer_name, $reservation_date, $reservation_time, $action)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "sandbox.smtp.mailtrap.io";
        $mail->SMTPAuth = true;
        $mail->Username = "9b74c958918c38";
        $mail->Password = "883fa6f655df7a";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom("restaurant@email.com", "Restaurant Name");
        $mail->addAddress($customer_email, $customer_name);

        $mail->isHTML(true);
        if ($action == "confirm") {
            $mail->Subject = "Reservation Confirmed";
            $mail->Body = "<p>Dear $customer_name,</p>
                <p>Your reservation for <strong>$reservation_date</strong> at <strong>$reservation_time</strong> has been confirmed.</p>
                <p>Thank you for the reservation. We will be waiting for your arrival.</p>";
        } elseif ($action == "cancel") {
            $mail->Subject = "Reservation Canceled";
            $mail->Body = "<p>Dear $customer_name,</p>
                <p>We regret to inform you that your reservation for <strong>$reservation_date</strong> at <strong>$reservation_time</strong> has been canceled.</p>
                <p>We apologize for the cancellation, no vacancies currently available for the requested reservation.</p>";
        }

        $mail->AltBody = strip_tags($mail->Body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
