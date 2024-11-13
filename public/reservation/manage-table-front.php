<?php
session_start();
require "../../config/db-connection.php";

$stmt = $conn->prepare("SELECT * FROM tables ORDER BY table_id");
$stmt->execute();
$tables = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tables</title>
</head>

<body>
    <h2>Manage Tables</h2>
    <div>
        <table>
            <tr>
                <th>Table ID</th>
                <th>Table Name</th>
                <th>Seat Count</th>
                <th>Available</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $tables->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["table_id"]; ?></td>
                    <td><?php echo htmlspecialchars($row["table_name"]); ?></td>
                    <td><?php echo $row["seat_count"]; ?></td>
                    <td><?php echo $row["available"] ? "Yes" : "No"; ?></td>
                    <td>
                        <button onclick="editTable(<?php echo $row['table_id']; ?>, '<?php echo htmlspecialchars($row['table_name']); ?>', <?php echo $row['seat_count']; ?>, <?php echo $row['available']; ?>)">Edit</button>
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
                <input type="text" id="table_name" name="table_name" required>
            </div>
            <div>
                <label for="seat_count">Seat Count:</label>
                <input type="number" id="seat_count" name="seat_count" required>
            </div>
            <div>
                <label for="available">Available:</label>
                <input type="checkbox" id="available" name="available">
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
                <input type="text" id="edit_table_name" name="edit_table_name" required>
            </div>
            <div>
                <label for="seat_count">Seat Count:</label>
                <input type="number" id="edit_seat_count" name="edit_seat_count" required>
            </div>
            <div>
                <label for="available">Available:</label>
                <input type="checkbox" id="edit_available" name="edit_available">
            </div>
            <button type="submit" name="update_table">Update Table</button>
            <button type="button" onclick="cancelChanges()">Cancel</button>
        </form>
    </div>
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
</body>

</html>