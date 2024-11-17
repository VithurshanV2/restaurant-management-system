<?php
session_start();
require "../../config/db-connection.php";

$selected_day = $_SESSION["selected_day"] ?? '';
if (isset($_GET["day"])) {
    $_SESSION["selected_day"] = $_GET["day"];
    $selected_day = $_SESSION["selected_day"];
}

$stmt = $conn->prepare("
    SELECT 
        reservation_id,
        customer_id,
        customer_name,
        reservation_time,
        party_size,
        notes
    FROM reservations
    WHERE reservation_date = ?
    ORDER BY reservation_time ASC
");
$stmt->bind_param("s", $selected_day);
$stmt->execute();
$reservations = $stmt->get_result();


$success_message = $_SESSION["success_message"] ?? null;
$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["success_message"], $_SESSION["errors"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee View Reservations</title>
    <link rel="stylesheet" href="/assets/css/manage-reservations.css">
</head>

<body>

    <?php include "../../includes/navbar_employee.php"; ?>

    <div>
        <h2>Reservations for <?php echo htmlspecialchars($selected_day ?: 'Selected Day'); ?></h2>
        <form action="employee-reservations-back.php" method="post">
            <label for="day">Select Day:</label>
            <input type="date" id="day" name="day" value="<?php echo htmlspecialchars($selected_day); ?>" required>
            <button type="submit" name="view_reservations">View Reservations</button>
        </form>
        <?php if ($reservations->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Reservation ID</th>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Reservation Time</th>
                    <th>Party Size</th>
                    <th>Notes</th>
                </tr>
                <?php while ($row = $reservations->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['reservation_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['reservation_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['party_size']); ?></td>
                        <td><?php echo htmlspecialchars($row['notes']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No reservations found for the selected day</p>
        <?php endif; ?>
    </div>

    <?php if ($success_message): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php include "../../includes/footer.php"; ?>

</body>

</html>