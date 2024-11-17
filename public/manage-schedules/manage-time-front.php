<?php
session_start();
require "../../config/db-connection.php";

$stmt = $conn->prepare("SELECT days_in_week, opening_time, closing_time, is_closed FROM restaurant_hours ORDER BY FIELD(days_in_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
$stmt->execute();
$schedule = $stmt->get_result();

$stmt = $conn->prepare("SELECT id, closed_date, reason FROM closed_dates ORDER BY closed_date ASC");
$stmt->execute();
$closed_dates = $stmt->get_result();

$selected_day = $_SESSION["selected_day"] ?? '';
if (isset($_GET["day"])) {
    $_SESSION["selected_day"] = $_GET["day"];
    $selected_day = $_SESSION["selected_day"];
}

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
    <title>Manage Restaurant Hours</title>
    <link rel="stylesheet" href="/assets/css/manage-hours.css">
</head>

<body>

    <?php include "../../includes/navbar_manager.php"; ?>

    <div>
        <div>
            <h2>Closed Dates</h2>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $closed_dates->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["closed_date"]); ?></td>
                        <td><?php echo htmlspecialchars($row["reason"]); ?></td>
                        <td>
                            <form action="manage-time-back.php" method="post" style="display: inline;">
                                <input type="hidden" name="remove_closed_date_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="remove_closed_date" onclick="return confirm('Are you sure you want to remove this closed date?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div>
            <button onclick="showAddClosedDate()">Add Closed Date</button>
        </div>
        <div id="add_closed_date" style="display: none;">
            <h2>Add Closed Date</h2>
            <form action="manage-time-back.php" method="post">
                <div>
                    <label for="date">Closed Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div>
                    <label for="reason">Reason (optional):</label>
                    <input type="text" id="reason" name="reason">
                </div>
                <button type="submit" name="add_closed_date">Add Closed Date</button>
                <button type="button" onclick="cancelChanges()">Cancel</button>
            </form>
        </div>
        <div>
            <h2>Manage Restaurant Hours</h2>
            <table>
                <tr>
                    <th>Day</th>
                    <th>Opening Time</th>
                    <th>Closing Time</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $schedule->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["days_in_week"]); ?></td>
                        <td><?php echo htmlspecialchars($row["opening_time"]); ?></td>
                        <td><?php echo htmlspecialchars($row["closing_time"]); ?></td>
                        <td>
                            <button onclick="editSchedule('<?php echo $row['days_in_week']; ?>', '<?php echo $row['opening_time']; ?>', '<?php echo $row['closing_time']; ?>')">Edit</button>
                            <?php if ($row["is_closed"] == 1): ?>
                                <form action="manage-time-back.php" method="post" style="display: inline;">
                                    <input type="hidden" name="toggle_close_day" value="1">
                                    <input type="hidden" name="day" value="<?php echo $row['days_in_week']; ?>">
                                    <input type="hidden" name="status" value="0">
                                    <button type="submit" onclick="return confirm('Are you sure you want to reopen this day?')">Reopen</button>
                                </form>
                            <?php else: ?>
                                <form action="manage-time-back.php" method="post" style="display: inline;">
                                    <input type="hidden" name="toggle_close_day" value="1">
                                    <input type="hidden" name="day" value="<?php echo $row['days_in_week']; ?>">
                                    <input type="hidden" name="status" value="1">
                                    <button type="submit" onclick="return confirm('Are you sure you want to close this day?')">Close Day</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div id="update_schedule" style="display: none;">
            <h2>Update Schedule</h2>
            <form action="manage-time-back.php" method="post">
                <input type="hidden" name="edit_day" id="edit_day">
                <div>
                    <label for="opening_time">Opening Time:</label>
                    <input type="time" id="opening_time" name="opening_time" required>
                </div>
                <div>
                    <label for="closing_time">Closing Time:</label>
                    <input type="time" id="closing_time" name="closing_time" required>
                </div>
                <button type="submit" name="update_schedule">Update Schedule</button>
                <button type="button" onclick="cancelChanges()">Cancel</button>
            </form>
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

        <script>
            function editSchedule(day, openingTime, closingTime) {
                document.getElementById("edit_day").value = day;
                document.getElementById("opening_time").value = openingTime;
                document.getElementById("closing_time").value = closingTime;
                document.getElementById("update_schedule").style.display = "block";
            }

            function showAddClosedDate() {
                document.getElementById("add_closed_date").style.display = "block";
            }

            function cancelChanges() {
                document.getElementById("add_closed_date").style.display = "none";
            }

            function cancelSchedule() {
                document.getElementById("update_schedule").style.display = "none";
            }
        </script>

        <?php include "../../includes/footer.php"; ?>

</body>

</html>