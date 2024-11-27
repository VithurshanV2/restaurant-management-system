<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../../vendor/autoload.php";

function sendPasswordResetEmail($recipient_email, $recipient_name, $reset_link)
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
        $mail->addAddress($recipient_email, $recipient_name);

        $mail->isHTML(true);
        $mail->Subject = "Password Reset Request";
        $mail->Body = "
            <p>Dear $recipient_name,</p>
            <p>We got a request to reset your restaurant account password. Click the link below to reset it:</p>
            <p><a href='$reset_link'>$reset_link</a></p>
            <p>If you ignore this message, your password will not be changed. If you didn't request a password reset, let us know.</p>
            <p>Thank you,</p>
            <p>Restaurant Name </p>
        ";
        $mail->AltBody = "Dear $recipient_name,\n\nWe got a request to reset your restaurant account password. Click the link below to reset it:\n\n$reset_link\n\nIf you ignore this message, your password will not be changed. If you didn't request a password reset, let us know.\n\nThank you,\nRestaurant Name";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
