<?php
require "../../includes/session.php";
check_access(["employee"]);

require "../../config/db-connection.php";

$stmt = $conn->prepare("SELECT days_in_week, opening_time, closing_time, is_closed FROM restaurant_hours");
$stmt->execute();
$hours = $stmt->get_result();

$stmt = $conn->prepare("SELECT closed_date, reason FROM closed_dates");
$stmt->execute();
$closed_days = $stmt->get_result();

$success_message = $_SESSION["success_message"] ?? null;
$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["success_message"], $_SESSION["errors"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee View Restaurant Schedules</title>
    <link rel="stylesheet" href="/assets/css/manage-hours.css">
</head>

<body>

    <?php include "../../includes/navbar_employee.php"; ?>

    <h2>Restaurant Schedules</h2>
    <div>
        <h2>Closed Days</h2>
        <?php if ($closed_days->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Closed Date</th>
                    <th>Reason</th>
                </tr>
                <?php while ($row = $closed_days->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['closed_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No closed days scheduled</p>
        <?php endif; ?>
    </div>
    <div>
        <h2>Restaurant Hours for the Week</h2>
        <table>
            <tr>
                <th>Day</th>
                <th>Opening Time</th>
                <th>Closing Time</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $hours->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['days_in_week']); ?></td>
                    <td><?php echo htmlspecialchars($row['opening_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['closing_time']); ?></td>
                    <td><?php echo $row['is_closed'] ? 'Closed' : 'Open'; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
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