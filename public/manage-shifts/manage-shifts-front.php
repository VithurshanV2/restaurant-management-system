<?php
session_start();
require "../../config/db-connection.php";

$current_date = date("Y-m-d");
$selected_date = $_GET["date"] ?? $_SESSION["selected_date"] ?? $current_date;
$_SESSION["selected_date"] = $selected_date;

$stmt = $conn->prepare("SELECT * FROM employees ORDER BY first_name ASC");
$stmt->execute();
$employees = $stmt->get_result();

$stmt = $conn->prepare("SELECT * FROM shifts JOIN employees ON shifts.employee_id = employees.employee_id WHERE shifts.shift_date = ? ORDER BY shift_start ASC");
$stmt->bind_param("s", $selected_date);
$stmt->execute();
$shifts = $stmt->get_result();

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
    <title>Manage Employee Shifts</title>
</head>

<body>
    <h2>Manage Employee Shifts</h2>
    <div>
        <form action="manage-shifts-front.php" method="get">
            <label for="date">Select Date: </label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($selected_date); ?>" required>
            <button type="submit">View Shifts</button>
        </form>
    </div>
    <div>
        <h2>Shifts for <?php echo htmlspecialchars($selected_date); ?></h2>
        <table>
            <tr>
                <th>Employee</th>
                <th>Shift Date</th>
                <th>Shift Time</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $shifts->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["first_name"] . " " . $row["last_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["shift_date"]); ?></td>
                    <td><?php echo htmlspecialchars($row["shift_start"]) . " - " . htmlspecialchars($row["shift_end"]); ?></td>
                    <td>
                        <button onclick="editShift('<?php echo $row['shift_id']; ?>', '<?php echo $row['employee_id']; ?>', '<?php echo $row['shift_date']; ?>', '<?php echo $row['shift_start']; ?>', '<?php echo $row['shift_end']; ?>')">Edit</button>
                        <form action="manage-shifts-back.php" method="post" style="display:inline;">
                            <input type="hidden" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">
                            <input type="hidden" name="shift_id" value="<?php echo $row['shift_id']; ?>">
                            <button type="submit" name="remove_shift" onclick="return confirm('Are you sure you want to remove this shift?')">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <button onclick="showAddShift()">Add Shift</button>
    <div id="add_shift" style="display: none;">
        <h2>Add New Shift</h2>
        <form action="manage-shifts-back.php" method="post">
            <div>
                <label for="employee_id">Employee:</label>
                <select id="employee_id" name="employee_id" required>
                    <option value="">Select Employee</option>
                    <?php while ($employee = $employees->fetch_assoc()): ?>
                        <option value="<?php echo $employee['employee_id']; ?>" <?php echo isset($form_data['employee_id']) && $form_data['employee_id'] == $employee['employee_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($employee['first_name'] . " " . $employee['last_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <?php if (isset($errors["employee_error"])): ?>
                    <span class="error-message"><?php echo $errors["employee_error"]; ?></span>
                <?php endif; ?>
            </div>
            <div>
                <label for="shift_date">Shift Date:</label>
                <input type="date" id="shift_date" name="shift_date" value="<?php echo isset($form_data['shift_date']) ? htmlspecialchars($form_data['shift_date']) : ''; ?>" required>
                <?php if (isset($errors["shift_date_error"])): ?>
                    <span class="error-message"><?php echo $errors["shift_date_error"]; ?></span>
                <?php endif; ?>
            </div>
            <div>
                <label for="shift_start">Start Time:</label>
                <input type="time" id="shift_start" name="shift_start" value="<?php echo isset($form_data['shift_start']) ? htmlspecialchars($form_data['shift_start']) : ''; ?>" required>
            </div>
            <div>
                <label for="shift_end">End Time:</label>
                <input type="time" id="shift_end" name="shift_end" value="<?php echo isset($form_data['shift_end']) ? htmlspecialchars($form_data['shift_end']) : ''; ?>" required>
            </div>
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">
            <button type="submit" name="add_shift">Add Shift</button>
            <button type="button" onclick="cancelChanges()">Cancel</button>
        </form>
    </div>

    <?php
    $stmt = $conn->prepare("SELECT * FROM employees ORDER BY first_name ASC");
    $stmt->execute();
    $employees = $stmt->get_result();
    ?>

    <div id="update_shift" style="display: none;">
        <h2>Update Shift</h2>
        <form action="manage-shifts-back.php" method="post">
            <input type="hidden" name="shift_id" id="edit_shift_id">
            <div>
                <label for="employee_id">Employee:</label>
                <select id="edit_employee_id" name="employee_id" required>
                    <option value="">Select Employee</option>
                    <?php while ($employee = $employees->fetch_assoc()): ?>
                        <option value="<?php echo $employee['employee_id']; ?>" <?php echo isset($form_data['employee_id']) && $form_data['employee_id'] == $employee['employee_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($employee['first_name'] . " " . $employee['last_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="shift_date">Shift Date:</label>
                <input type="date" id="edit_shift_date" name="shift_date" required>
            </div>
            <div>
                <label for="shift_start">Start Time:</label>
                <input type="time" id="edit_shift_start" name="shift_start" required>
            </div>
            <div>
                <label for="shift_end">End Time:</label>
                <input type="time" id="edit_shift_end" name="shift_end" required>
            </div>
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">
            <button type="submit" name="update_shift">Update Shift</button>
            <button type="button" onclick="cancelEdit()">Cancel</button>
        </form>
    </div>
    <div>
        <h2>Duplicate Shifts</h2>
        <form action="manage-shifts-back.php" method="post">
            <label for="duplicate_date">Select Date to Duplicate:</label>
            <input type="date" id="duplicate_date" name="duplicate_date" value="<?php echo htmlspecialchars($selected_date); ?>" required>

            <label for="target_date">Select Target Date:</label>
            <input type="date" id="target_date" name="target_date" required>

            <button type="submit" name="duplicate_shifts">Duplicate Shifts</button>
        </form>
    </div>

    <?php if ($success_message): ?>
        <p><?php echo htmlspecialchars($success_message); ?></p>
        <?php unset($_SESSION["success_message"]); ?>
    <?php endif; ?>

    <script>
        function showAddShift() {
            document.getElementById('add_shift').style.display = 'block';
        }

        function cancelChanges() {
            document.getElementById('add_shift').style.display = 'none';
        }

        function editShift(shiftId, employeeId, shiftDate, shiftStart, shiftEnd) {
            document.getElementById("edit_shift_id").value = shiftId;
            document.getElementById("edit_employee_id").value = employeeId;
            document.getElementById("edit_shift_date").value = shiftDate;
            document.getElementById("edit_shift_start").value = shiftStart;
            document.getElementById("edit_shift_end").value = shiftEnd;
            document.getElementById("update_shift").style.display = "block";
        }

        function cancelEdit() {
            document.getElementById("update_shift").style.display = "none";
        }
    </script>
</body>

</html>