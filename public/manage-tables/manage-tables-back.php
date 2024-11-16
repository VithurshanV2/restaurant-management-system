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
        } elseif (strlen($table_name) < 1 || strlen($table_name) > 20) {
            $errors["table_name_error"] = "Table name must be within 20 characters";
        }
        if (empty($seat_count) || !is_numeric($seat_count)) {
            $errors["seat_count_error"] = "Please enter a valid seat count";
        } elseif ($seat_count < 1 || $seat_count > 20) {
            $errors["seat_count_error"] = "Seat count must be between 1 and 20";
        }


        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT MAX(position) AS max_position FROM tables");
            $stmt->execute();
            $current_position = $stmt->get_result()->fetch_assoc();
            $new_position = $current_position['max_position'] + 1;

            $stmt = $conn->prepare("INSERT INTO tables (table_name, seat_count, available, position) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siii", $table_name, $seat_count, $available, $new_position);
            $stmt->execute();
            $_SESSION["success_message"] = "Table added successfully";
        } else {
            $_SESSION["errors"] = $errors;
            $_SESSION["form_data"] = $form_data;
        }
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
    } else {
        $_SESSION["errors"] = $errors;
        $_SESSION["form_data"] = $form_data;
    }
    header("Location: manage-tables-front.php");
    exit();
}

if (isset($_POST["remove_table"])) {
    $table_id = trim($_POST["remove_table_id"]);
    $form_data = $_POST;

    if (empty($table_id) || !is_numeric($table_id)) {
        $errors["remove_table_id_error"] = "Invalid table ID";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM reservations_tables WHERE table_id = ?");
        $stmt->bind_param("i", $table_id);
        $stmt->execute();
        $reservationResult = $stmt->get_result();

        if ($reservationResult->num_rows > 0) {
            $errors["remove_table_error"] = "Table cannot be removed because it has existing reservations";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("DELETE FROM tables WHERE table_id = ?");
        $stmt->bind_param("i", $table_id);

        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Table removed successfully";
        } else {
            $errors["remove_table_error"] = "Error removing table";
        }
    }

    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        $_SESSION["form_data"] = $form_data;
    }
    header("Location: manage-tables-front.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["move_up"])) {
        $table_id = $_POST["table_id"];

        $stmt = $conn->prepare("SELECT position FROM tables WHERE table_id = ?");
        $stmt->bind_param("i", $table_id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();

        $stmt = $conn->prepare("SELECT table_id, position FROM tables WHERE position < ? ORDER BY position DESC LIMIT 1");
        $stmt->bind_param("i", $current["position"]);
        $stmt->execute();
        $above = $stmt->get_result()->fetch_assoc();

        if ($above) {
            $conn->query("UPDATE tables SET position = {$above['position']} WHERE table_id = {$table_id}");
            $conn->query("UPDATE tables SET position = {$current['position']} WHERE table_id = {$above['table_id']}");
            $_SESSION["success_message"] = "Table moved up successfully";
        }
    }

    if (isset($_POST["move_down"])) {
        $table_id = $_POST["table_id"];

        $stmt = $conn->prepare("SELECT position FROM tables WHERE table_id = ?");
        $stmt->bind_param("i", $table_id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();

        $stmt = $conn->prepare("SELECT table_id, position FROM tables WHERE position > ? ORDER BY position ASC LIMIT 1");
        $stmt->bind_param("i", $current["position"]);
        $stmt->execute();
        $below = $stmt->get_result()->fetch_assoc();

        if ($below) {
            $conn->query("UPDATE tables SET position = {$below['position']} WHERE table_id = {$table_id}");
            $conn->query("UPDATE tables SET position = {$current['position']} WHERE table_id = {$below['table_id']}");
            $_SESSION["success_message"] = "Table moved down successfully";
        }
    }

    header("Location: manage-tables-front.php");
    exit();
}

$conn->close();
