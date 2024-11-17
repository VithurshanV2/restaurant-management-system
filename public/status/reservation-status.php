<?php
session_start();
require "../../config/db-connection.php";

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../login/login-front.php");
    exit;
}

$customer_id = $_SESSION["customer_id"];

$stmt = $conn->prepare("
    SELECT 
        customer_name,
        reservation_date,
        reservation_time,
        party_size,
        notes,
        status
    FROM reservations
    WHERE customer_id = ?
    ORDER BY reservation_date DESC, reservation_time ASC
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$reservations = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Reservations</title>
    <link rel="stylesheet" href="/assets/css/manage-reservations.css">
</head>

<body>
    <!-- Include Navigation Bar -->
    <?php include 'navbar.php'; ?>
    
    <div>
        <h2>Your Reservations</h2>

        <?php if ($reservations->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Customer Name</th>
                    <th>Reservation Date</th>
                    <th>Reservation Time</th>
                    <th>Party Size</th>
                    <th>Notes</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = $reservations->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['reservation_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['reservation_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['party_size']); ?></td>
                        <td><?php echo htmlspecialchars($row['notes'] ?: 'Not specified'); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No reservations found</p>
        <?php endif; ?>
    </div>
    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

</body>

</html>

<?php
$conn->close();
?>
