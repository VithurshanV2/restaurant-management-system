<?php
session_start();
require "../../config/db-connection.php";

$current_date = isset($_GET["date"]) ? $_GET["date"] : (isset($_SESSION["form_data"]["date"]) ? $_SESSION["form_data"]["date"] : date('Y-m-d'));

$stmt = $conn->prepare("SELECT * FROM reservations WHERE status = 'pending' ORDER BY reservation_time ASC");
$stmt->execute();
$reservations_pending = $stmt->get_result();

$stmt = $conn->prepare("SELECT * FROM reservations WHERE status = 'confirmed' AND reservation_date = ? ORDER BY reservation_time ASC");
$stmt->bind_param("s", $current_date);
$stmt->execute();
$reservations_confirmed = $stmt->get_result();

$success_message = $_SESSION["success_message"] ?? null;
$errors = $_SESSION["errors"] ?? [];
$form_data = $_SESSION["form_data"] ?? [];
unset($_SESSION["success_message"], $_SESSION["errors"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations</title>
    <link rel="stylesheet" href="/assets/css/manage-reservations.css">
</head>

<body>

    <?php include "../../includes/navbar_manager.php"; ?>

    <h2>Manage Reservations</h2>

    <div>
        <h2>Pending Reservations</h2>
        <table>
            <tr>
                <th>Customer Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Party Size</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $reservations_pending->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["customer_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["reservation_date"]); ?></td>
                    <td><?php echo htmlspecialchars($row["reservation_time"]); ?></td>
                    <td><?php echo htmlspecialchars($row["party_size"]); ?></td>
                    <td><?php echo htmlspecialchars($row["notes"]); ?></td>
                    <td>
                        <form action="manage-reservation-back.php" method="post" style="display: inline;">
                            <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                            <button type="submit" name="action" value="confirm" onclick="return confirm('Are you sure you want to confirm this reservation?')">Confirm</button>
                            <button type="submit" name="action" value="cancel" onclick="return confirm('Are you sure you want to cancel this reservation?')">Cancel</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div>
        <h2>Confirmed Reservations (Date: <?php echo htmlspecialchars($current_date); ?>)</h2>
        <form action="manage-reservation-back.php" method="post">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($current_date); ?>">
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($current_date); ?>" onchange="this.form.submit()">
        </form>
        <table>
            <tr>
                <th>Customer Name</th>
                <th>Time</th>
                <th>Party Size</th>
                <th>Notes</th>
            </tr>
            <?php while ($row = $reservations_confirmed->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["customer_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["reservation_time"]); ?></td>
                    <td><?php echo htmlspecialchars($row["party_size"]); ?></td>
                    <td><?php echo htmlspecialchars($row["notes"]); ?></td>
                    <td>
                        <form action="remove.php" method="post" style="display:inline;">
                            <input type="hidden" name="remove_reservation_id" value="<?php echo $row['reservation_id']; ?>">
                            <button type="submit" name="remove_reservation" value="true" onclick="return confirm('Are you sure you want to remove this reservation?')">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <?php if ($success_message): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
        <?php unset($_SESSION["success_message"]); ?>
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