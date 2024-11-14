<?php
session_start();
require "../../config/db-connection.php";

$stmt = $conn->prepare("SELECT days_in_week, opening_time, closing_time FROM restaurant_hours ORDER BY FIELD(days_in_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
$stmt->execute();
$result = $stmt->get_result();

$success_message = isset($_SESSION["success_message"]) ? $_SESSION["success_message"] : '';
$errors = isset($_SESSION["errors"]) ? $_SESSION["errors"] : [];
$form_data = isset($_SESSION["form_data"]) ? $_SESSION["form_data"] : [];
unset($_SESSION["errors"], $_SESSION["form_data"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules</title>
</head>

<body>
    <div>
        <table>
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Opening Time</th>
                    <th>Closing Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['days_in_week']); ?></td>
                        <td><?php echo htmlspecialchars($row['opening_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['closing_time']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
    <div>
        <form action="manage-time-back.php" method="post">
            <div>
                <label for="day">Select Day:</label>
                <select name="days_in_week" id="days_in_week">
                    <option value="Monday" <?php echo isset($form_data['days_in_week']) && $form_data['days_in_week'] == 'Monday' ? 'selected' : ''; ?>>Monday</option>
                    <option value="Tuesday" <?php echo isset($form_data['days_in_week']) && $form_data['days_in_week'] == 'Tuesday' ? 'selected' : ''; ?>>Tuesday</option>
                    <option value="Wednesday" <?php echo isset($form_data['days_in_week']) && $form_data['days_in_week'] == 'Wednesday' ? 'selected' : ''; ?>>Wednesday</option>
                    <option value="Thursday" <?php echo isset($form_data['days_in_week']) && $form_data['days_in_week'] == 'Thursday' ? 'selected' : ''; ?>>Thursday</option>
                    <option value="Friday" <?php echo isset($form_data['days_in_week']) && $form_data['days_in_week'] == 'Friday' ? 'selected' : ''; ?>>Friday</option>
                    <option value="Saturday" <?php echo isset($form_data['days_in_week']) && $form_data['days_in_week'] == 'Saturday' ? 'selected' : ''; ?>>Saturday</option>
                    <option value="Sunday" <?php echo isset($form_data['days_in_week']) && $form_data['days_in_week'] == 'Sunday' ? 'selected' : ''; ?>>Sunday</option>
                </select>
                <?php if (isset($errors["day_error"])): ?>
                    <span class="error-message"><?php echo $errors["day_error"]; ?></span>
                <?php endif; ?>
            </div>
            <div>
                <label for="opening_time">Opening Time:</label>
                <input type="time" name="opening_time" value="<?php echo isset($form_data['opening_time']) ? htmlspecialchars($form_data['opening_time']) : ''; ?>" required>
                <?php if (isset($errors["opening_time_error"])): ?>
                    <span class="error-message"><?php echo $errors["opening_time_error"]; ?></span>
                <?php endif; ?>
            </div>

            <div>
                <label for="closing_time">Closing Time:</label>
                <input type="time" name="closing_time" value="<?php echo isset($form_data['closing_time']) ? htmlspecialchars($form_data['closing_time']) : ''; ?>" required>
                <?php if (isset($errors["closing_time_error"])): ?>
                    <span class="error-message"><?php echo $errors["closing_time_error"]; ?></span>
                <?php endif; ?>
                <?php if (isset($errors["time_error"])): ?>
                    <span class="error-message"><?php echo $errors["time_error"]; ?></span>
                <?php endif; ?>
            </div>
            <button type="submit">Save</button>
        </form>
        <?php if ($success_message): ?>
            <p><?php echo htmlspecialchars($success_message); ?></p>
            <?php unset($_SESSION["success_message"]); ?>
        <?php endif; ?>
        <?php if (isset($errors["update_error"])): ?>
            <p><?php echo $errors["update_error"]; ?></p>
        <?php endif; ?>
        <?php if (isset($errors["insert_error"])): ?>
            <p class="error-message"><?php echo $errors["insert_error"]; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>