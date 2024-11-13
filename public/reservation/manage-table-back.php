<?php
session_start();
require "../../config/db-connection.php";

$errors = [];
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_table"])) {
        $table_name = trim($_POST["table_name"]);
        $seat_count = trim($_POST["seat_count"]);
        $available = isset($_POST["available"]) ? 1 : 0;
        $form_data = $_POST;

        if (empty($table_name)) {
            $errors["table_name_error"] = "Table name is required";
        }
        if (empty($seat_count) || !is_numeric($seat_count)) {
            $errors["seat_count_error"] = "Please enter a valid seat count";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO tables (table_name, seat_count, available) VALUES (?, ?, ?)");
            $stmt->bind_param("sii", $table_name, $seat_count, $available);
            $stmt->execute();
            $_SESSION["success_message"] = "Table added successfully";
            unset($_SESSION["form_data"], $_SESSION["errors"]);
            header("Location: manage-tables-front.php");
            exit();
        }
    }

    if (isset($_POST["update_table"])) {
        $table_id = trim($_POST["edit_table_id"]);
        $table_name = trim($_POST["edit_table_name"]);
        $seat_count = trim($_POST["edit_seat_count"]);
        $available = isset($_POST["edit_available"]) ? 1 : 0;

        if (empty($table_name)) {
            $errors["edit_table_name_error"] = "Table name is required";
        }
        if (empty($seat_count) || !is_numeric($seat_count)) {
            $errors["edit_seat_count_error"] = "Please enter a valid seat count";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE tables SET table_name = ?, seat_count = ?, available = ? WHERE table_id = ?");
            $stmt->bind_param("siii", $table_name, $seat_count, $available, $table_id);
            $stmt->execute();
            $_SESSION["success_message"] = "Table updated successfully";
            unset($_SESSION["form_data"], $_SESSION["errors"]);
            header("Location: manage-tables-front.php");
            exit();
        }
    }
}

$conn->close();
