<?php
session_start();
require "../../config/db-connection.php";

$stmt = $conn->prepare("SELECT days_in_week, opening_time, closing_time, is_closed FROM restaurant_hours ORDER BY FIELD(days_in_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
$stmt->execute();
$schedule = $stmt->get_result();

$stmt = $conn->prepare("SELECT id, closed_date, reason FROM closed_dates ORDER BY closed_date ASC");
$stmt->execute();
$closed_dates = $stmt->get_result();

$selected_day = $_SESSION['selected_day'] ?? '';
if (isset($_GET['day'])) {
    $_SESSION['selected_day'] = $_GET['day'];
    $selected_day = $_SESSION['selected_day'];
}

$stmt = $conn->prepare("SELECT id, day, start_time, end_time FROM time_slots WHERE day = ? ORDER BY start_time");
$stmt->bind_param('s', $selected_day);
$stmt->execute();
$time_slots = $stmt->get_result();

$stmt = $conn->prepare("SELECT DISTINCT day FROM time_slots ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
$stmt->execute();
$days = $stmt->get_result();

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
</head>

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
        <h2>Manage Time Slots</h2>
        <form action="" method="get">
            <label for="day">Select Day:</label>
            <select name="day" id="day" onchange="this.form.submit()">
                <option value="">Select a day</option>
                <?php while ($row = $days->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row["day"]); ?>" <?php echo $selected_day == $row["day"] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row["day"]); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <?php if ($selected_day): ?>
            <table>
                <tr>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $time_slots->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["start_time"]); ?></td>
                        <td><?php echo htmlspecialchars($row["end_time"]); ?></td>
                        <td>
                            <form action="manage-time-back.php" method="post" style="display: inline;">
                                <input type="hidden" name="slot_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="remove_time_slot" onclick="return confirm('Are you sure you want to remove this time slot?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
        <div>
            <button onclick="showAddTimeSlot()">Add Time Slot</button>
        </div>
        <div id="add_time_slot" style="display: none;">
            <h3>Add Time Slot</h3>
            <form action="manage-time-back.php" method="post">
                <div>
                    <label for="day">Day:</label>
                    <select name="day" id="day" required>
                        <option value="">Select a day</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div>
                    <label for="start_time">Start Time:</label>
                    <input type="time" id="start_time" name="start_time" required>
                </div>
                <div>
                    <label for="end_time">End Time:</label>
                    <input type="time" id="end_time" name="end_time" required>
                </div>
                <button type="submit" name="add_time_slot">Add Time Slot</button>
                <button type="button" onclick="cancelTimeSlot()">Cancel</button>
            </form>
        </div>
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

        function showAddTimeSlot() {
            document.getElementById("add_time_slot").style.display = "block";
        }

        function cancelTimeSlot() {
            document.getElementById("add_time_slot").style.display = "none";
        }

        function cancelSchedule() {
            document.getElementById("update_schedule").style.display = "none";
        }
    </script>
    </body>

</html>