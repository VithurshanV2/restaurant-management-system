<?php
require "../../includes/session.php";
check_access(["employee"]);

require "../../config/db-connection.php";

$selected_day = $_SESSION["selected_day"] ?? '';
if (isset($_GET["day"])) {
    $_SESSION["selected_day"] = $_GET["day"];
    $selected_day = $_SESSION["selected_day"];
}

$stmt = $conn->prepare("
    SELECT 
        shifts.employee_id,
        employees.first_name,
        employees.last_name,
        shifts.shift_start,
        shifts.shift_end
    FROM shifts
    INNER JOIN employees ON shifts.employee_id = employees.employee_id
    WHERE shifts.shift_date = ?
    ORDER BY shifts.shift_start ASC
");
$stmt->bind_param("s", $selected_day);
$stmt->execute();
$shifts = $stmt->get_result();

$success_message = $_SESSION["success_message"] ?? null;
$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["success_message"], $_SESSION["errors"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee View Shifts</title>
    <link rel="stylesheet" href="/assets/css/manage-shifts.css">
</head>

<body>

    <?php include "../../includes/navbar_employee.php"; ?>

    <div>
        <h2>Shifts for <?php echo htmlspecialchars($selected_day ?: 'Selected Day'); ?></h2>
        <form action="employee-shifts-back.php" method="post">
            <label for="day">Select Day:</label>
            <input type="date" id="day" name="day" value="<?php echo htmlspecialchars($selected_day); ?>" required>
            <button type="submit" name="view_shifts">View Shifts</button>
        </form>
        <?php if ($shifts->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Shift Start</th>
                    <th>Shift End</th>
                </tr>
                <?php while ($row = $shifts->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['employee_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['shift_start']); ?></td>
                        <td><?php echo htmlspecialchars($row['shift_end']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No shifts found for the selected day</p>
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