<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: index.php"); exit;
}

$customer_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $booking_id = intval($_POST['cancel_booking_id']);

    $stmt = $conn->prepare("UPDATE Booking SET Status = 'cancelled' WHERE Booking_ID = ? AND Customer_ID = ? AND Status = 'pending'");
    $stmt->bind_param("ii", $booking_id, $customer_id);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT Name FROM Customer WHERE Customer_ID = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->bind_result($customer_name);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM Booking WHERE Customer_ID = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->bind_result($total_bookings);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM Booking WHERE Customer_ID = ? AND Status = 'pending'");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->bind_result($pending_payments);
$stmt->fetch();
$stmt->close();

$loyalty_points = $total_bookings * 5;

$stmt = $conn->prepare("SELECT b.Booking_ID, s.Service_Name, b.Booking_Date, b.Booking_Time, b.Status
                        FROM Booking b
                        JOIN Booking_Details bd ON b.Booking_ID = bd.Booking_ID
                        JOIN Service s ON bd.Service_ID = s.Service_ID
                        WHERE b.Customer_ID = ?
                        ORDER BY b.Booking_Date DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>  

<?php include 'includes/header.php'; ?>
<div class="customer-dashboard">
    <div class="header">Welcome Back, <?= htmlspecialchars($customer_name) ?>!</div>

    <div class="stats">
        <div class="card">
            <div class="card-title">Total Bookings</div>
            <div class="card-value"><?= $total_bookings ?></div>
        </div>
        <div class="card">
            <div class="card-title">Pending Payments</div>
            <div class="card-value"><?= $pending_payments ?></div>
        </div>
        <div class="card">
            <div class="card-title">Loyalty Points</div>
            <div class="card-value"><?= $loyalty_points ?></div>
        </div>
    </div>

    <h4>Booking History</h4>
    <a href="book_service.php" class="btn btn-primary mb-3">Book a Service</a>

    <table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $bookings->fetch_assoc()): ?>
            <tr>
                <td><?= $row['Booking_ID'] ?></td>
                <td><?= $row['Service_Name'] ?></td>
                <td><?= $row['Booking_Date'] ?></td>
                <td><?= $row['Booking_Time'] ?></td>
                <td><?= ucfirst($row['Status']) ?></td>
                <td>
                  <?php if (strtolower($row['Status']) === 'pending'): ?>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                        <input type="hidden" name="cancel_booking_id" value="<?= $row['Booking_ID'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted"></span>
                  <?php endif; ?>
                </td>
            </tr>   
        <?php endwhile; ?>  
    </tbody>
</table>
</div>

<?php include 'includes/footer.php'; ?>
