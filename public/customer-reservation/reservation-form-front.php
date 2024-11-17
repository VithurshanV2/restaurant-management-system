<?php
session_start();
require "../../config/db-connection.php";

$success_message = $_SESSION["success_message"] ?? null;
$errors = $_SESSION["errors"] ?? [];
$form_data = $_SESSION["form_data"] ?? [];
unset($_SESSION["success_message"], $_SESSION["errors"], $_SESSION["form_data"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Form</title>
    <link rel="stylesheet" href="/assets/css/reservations.css">
</head>
<h1>Reservation</h1>
<nav>
    <a href="/public/home/home.html">Home</a>
    <a href="/public/about/about.html">About Us</a>
    <a href="/public/menu/menu.html">Menu</a>
    <a href="/public/feedback/feedback.html">Feedback</a>
    <a href="/public/status/reservation-status.php">Reservation Status</a>
    <a href="/public/login/login-front.php">Login</a>
</nav>

<body>
    <h2>Reservation Form</h2>
    <form action="reservation-form-back.php" method="POST">
        <div>
            <label for="date">Reservation Date:</label>
            <input type="date" id="date" name="reservation_date" value="<?php echo isset($form_data['reservation_date']) ? htmlspecialchars($form_data['reservation_date']) : ''; ?>" required>
            <?php if (isset($errors['date_error'])): ?>
                <span class="error-message"><?php echo $errors['date_error']; ?></span>
            <?php endif; ?>
        </div>

        <div>
            <label for="time">Reservation Time:</label>
            <input type="time" id="time" name="reservation_time" value="<?php echo isset($form_data['reservation_time']) ? htmlspecialchars($form_data['reservation_time']) : ''; ?>" required>
            <?php if (isset($errors['time_error'])): ?>
                <span class="error-message"><?php echo $errors['time_error']; ?></span>
            <?php endif; ?>
        </div>

        <div>
            <label for="party_size">Party Size:</label>
            <input type="number" id="party_size" name="party_size" min="1" value="<?php echo isset($form_data['party_size']) ? htmlspecialchars($form_data['party_size']) : ''; ?>" required>
            <?php if (isset($errors['party_size_error'])): ?>
                <span class="error-message"><?php echo $errors['party_size_error']; ?></span>
            <?php endif; ?>
        </div>

        <div>
            <label for="customer_name">Your Name:</label>
            <input type="text" id="customer_name" name="customer_name" value="<?php echo isset($form_data['customer_name']) ? htmlspecialchars($form_data['customer_name']) : ''; ?>" required>
        </div>

        <div>
            <label for="notes">Special Requests:</label>
            <textarea id="notes" name="notes"><?php echo isset($form_data['notes']) ? htmlspecialchars($form_data['notes']) : ''; ?></textarea>
        </div>

        <button type="submit">Request Reservation</button>
    </form>

    <?php if ($success_message): ?>
        <p class="error-messages"><?php echo htmlspecialchars($success_message); ?></p>
        <?php unset($_SESSION["success_message"]); ?>
    <?php endif; ?>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

</body>

</html>