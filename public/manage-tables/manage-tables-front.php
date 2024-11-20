<?php
require "../../includes/session.php";
check_access(["manager"]);

require "../../config/db-connection.php";

$stmt = $conn->prepare("SELECT * FROM tables ORDER BY position ASC");
$stmt->execute();
$tables = $stmt->get_result();

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
    <title>Manage Tables</title>
    <link rel="stylesheet" href="/assets/css/manage-tables.css">
</head>

<body>

    <?php include "../../includes/navbar_manager.php"; ?>

    <h2>Manage Tables</h2>
    <div>
        <table>
            <tr>
                <th>Table Name</th>
                <th>Seat Count</th>
                <th>Available</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $tables->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["table_name"]); ?></td>
                    <td><?php echo $row["seat_count"]; ?></td>
                    <td><?php echo $row["available"] ? "Yes" : "No"; ?></td>
                    <td>
                        <form action="manage-tables-back.php" method="post" style="display:inline;">
                            <input type="hidden" name="table_id" value="<?php echo $row['table_id']; ?>">
                            <button type="submit" name="move_up" <?php echo ($row["position"] == 1) ? "disabled" : ""; ?>>Move Up</button>
                            <button type="submit" name="move_down">Move Down</button>
                        </form>

                        <button onclick="editTable(<?php echo $row['table_id']; ?>, '<?php echo htmlspecialchars($row['table_name']); ?>', <?php echo $row['seat_count']; ?>, <?php echo $row['available']; ?>)">Edit</button>
                        <form action="manage-tables-back.php" method="post" style="display:inline;">
                            <input type="hidden" name="remove_table_id" value="<?php echo $row['table_id']; ?>">
                            <button type="submit" name="remove_table" onclick="return confirm('Are you sure you want to remove this table?')">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <div>
        <button onclick="showAddTable()">Add Table</button>
    </div>
    <div id="add_table" style="display: none;">
        <h2>Add Tables</h2>
        <form action="manage-tables-back.php" method="post">
            <div>
                <label for="table_name">Table Name:</label>
                <input type="text" id="table_name" name="table_name" maxlength="20" value="<?php echo isset($form_data['table_name']) ? htmlspecialchars($form_data['table_name']) : ''; ?>" required>
                <?php if (isset($errors["table_name_error"])): ?>
                    <span class="error-message">
                        <?php echo $errors["table_name_error"]; ?>
                    </span>
                <?php endif; ?>
            </div>
            <div>
                <label for="seat_count">Seat Count:</label>
                <input type="number" id="seat_count" name="seat_count" min="1" max="20" value="<?php echo isset($form_data['seat_count']) ? htmlspecialchars($form_data['seat_count']) : ''; ?>" required>
                <?php if (isset($errors["seat_count_error"])): ?>
                    <span class="error-message">
                        <?php echo $errors["seat_count_error"]; ?>
                    </span>
                <?php endif; ?>
            </div>
            <div>
                <label for="available">Available:</label>
                <input type="checkbox" id="available" name="available" <?php echo isset($form_data['available']) && $form_data['available'] ? 'checked' : ''; ?>>
            </div>
            <button type="submit" name="add_table">Add Table</button>
            <button type="button" onclick="cancelChanges()">Cancel</button>
        </form>
    </div>
    <div id="edit_table" style="display: none;">
        <h2>Edit Tables</h2>
        <form action="manage-tables-back.php" method="post">
            <input type="hidden" id="edit_table_id" name="edit_table_id">
            <div>
                <label for="table_name">Table Name:</label>
                <input type="text" id="edit_table_name" name="edit_table_name" value="<?php echo isset($form_data['edit_table_name']) ? htmlspecialchars($form_data['edit_table_name']) : ''; ?>" required>
                <?php if (isset($errors["edit_table_name_error"])): ?>
                    <span class="error-message">
                        <?php echo $errors["edit_table_name_error"]; ?>
                    </span>
                <?php endif; ?>
            </div>
            <div>
                <label for="seat_count">Seat Count:</label>
                <input type="number" id="edit_seat_count" name="edit_seat_count" value="<?php echo isset($form_data['edit_seat_count']) ? htmlspecialchars($form_data['edit_seat_count']) : ''; ?>" required>
                <?php if (isset($errors["edit_seat_count_error"])): ?>
                    <span class="error-message">
                        <?php echo $errors["edit_seat_count_error"]; ?>
                    </span>
                <?php endif; ?>
            </div>
            <div>
                <label for="available">Available:</label>
                <input type="checkbox" id="edit_available" name="edit_available" <?php echo isset($form_data['edit_available']) && $form_data['edit_available'] ? 'checked' : ''; ?>>
            </div>
            <button type="submit" name="update_table">Update Table</button>
            <button type="button" onclick="cancelChanges()">Cancel</button>
        </form>
    </div>

    <?php if ($success_message): ?>
        <p><?php echo htmlspecialchars($success_message); ?></p>
        <?php unset($_SESSION["success_message"]); ?>
    <?php endif; ?>

    <script>
        function showAddTable() {
            document.getElementById('add_table').style.display = 'block';
            document.getElementById('edit_table').style.display = 'none';
        }

        function cancelChanges() {
            document.getElementById('add_table').style.display = 'none';
            document.getElementById('edit_table').style.display = 'none';
        }

        function editTable(tableId, tableName, seatCount, available) {
            document.getElementById('edit_table_id').value = tableId;
            document.getElementById('edit_table_name').value = tableName;
            document.getElementById('edit_seat_count').value = seatCount;
            document.getElementById('edit_available').checked = (available == 1);
            document.getElementById('edit_table').style.display = 'block';
            document.getElementById('add_table').style.display = 'none';

        }
    </script>

    <?php include "../../includes/footer.php"; ?>

</body>

</html>