<?php
require "../../includes/session.php";
require "../../config/db-connection.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["view_shifts"])) {
    $selected_day = trim($_POST["day"]);

    if (empty($selected_day)) {
        $errors[] = "Please select a valid date";
    }

    if (empty($errors)) {
        $_SESSION["selected_day"] = $selected_day;
        $_SESSION["success_message"] = "Shifts loaded successfully";
    } else {
        $_SESSION["errors"] = $errors;
    }

    header("Location: employee-shifts-front.php");
    exit();
}

$conn->close();
