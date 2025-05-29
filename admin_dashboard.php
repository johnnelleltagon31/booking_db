<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);

    if (in_array($action, ['confirm', 'complete'])) {
        $new_status = $action === 'confirm' ? 'confirmed' : 'completed';
        $stmt = $conn->prepare("UPDATE Booking SET Status = ? WHERE Booking_ID = ?");
        $stmt->bind_param("si", $new_status, $id);
        $stmt->execute();
        header("Location: admin_dashboard.php");
        exit;
    }
}

$total_customers = $conn->query("SELECT COUNT(*) AS total FROM Customer")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) AS total FROM Booking")->fetch_assoc()['total'];
$confirm_bookings = $conn->query("SELECT COUNT(*) AS total FROM Booking WHERE Status = 'confirmed'")->fetch_assoc()['total'];
$complete_bookings = $conn->query("SELECT COUNT(*) AS total FROM Booking WHERE Status = 'completed'")->fetch_assoc()['total'];
$cancelled_bookings = $conn->query("SELECT COUNT(*) AS total FROM Booking WHERE Status = 'cancelled'")->fetch_assoc()['total'];
$pending_bookings = $conn->query("SELECT COUNT(*) AS total FROM Booking WHERE Status = 'pending'")->fetch_assoc()['total'];

$recent = $conn->query("
    SELECT 
        b.Booking_ID, 
        c.Name AS Customer, 
        GROUP_CONCAT(s.Service_Name SEPARATOR ', ') AS Services,
        b.Booking_Date, 
        b.Booking_Time, 
        b.Status 
    FROM Booking b
    JOIN Customer c ON b.Customer_ID = c.Customer_ID
    JOIN Booking_Details bd ON b.Booking_ID = bd.Booking_ID
    JOIN Service s ON bd.Service_ID = s.Service_ID
    GROUP BY b.Booking_ID
    ORDER BY b.Booking_Date DESC, b.Booking_Time DESC
    LIMIT 10
");
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-5">
  <h2 class="mb-4 text-center">Admin Dashboard</h2>

  <div class="row text-center mb-4">
    <div class="col-md-4">
      <div class="card bg-light p-3">
        <h5>Total Customers</h5>
        <p class="fs-3"><?= $total_customers ?></p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-light p-3">
        <h5>Total Bookings</h5>
        <p class="fs-3"><?= $total_bookings ?></p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-light p-3">
        <h5>Pending Bookings</h5>
        <p class="fs-3"><?= $pending_bookings ?></p>
      </div>
    </div>
  </div>

  <div class="row text-center mb-4">
    <div class="col-md-4">
      <div class="card bg-light p-3">
        <h5>Confirm Bookings</h5>
        <p class="fs-3"><?= $confirm_bookings ?></p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-light p-3">
        <h5>Complete Bookings</h5>
        <p class="fs-3"><?= $complete_bookings ?></p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-light p-3">
        <h5>Cancelled Bookings</h5>
        <p class="fs-3"><?= $cancelled_bookings ?></p>
      </div>
    </div>
  </div>

  <h4>Recent Bookings</h4>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Services</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $recent->fetch_assoc()): 
        $status = strtolower(trim($row['Status']));
      ?>
      <tr>
        <td><?= $row['Booking_ID'] ?></td>
        <td><?= htmlspecialchars($row['Customer']) ?></td>
        <td><?= htmlspecialchars($row['Services']) ?></td>
        <td><?= $row['Booking_Date'] ?></td>
        <td><?= $row['Booking_Time'] ?></td>
        <td>
          <?php if ($status === 'pending'): ?>
            <a href="?action=confirm&id=<?= $row['Booking_ID'] ?>" class="btn btn-sm btn-warning ms-2">Confirm</a>
          <?php elseif ($status === 'confirmed'): ?>
            <a href="?action=complete&id=<?= $row['Booking_ID'] ?>" class="btn btn-sm btn-success ms-1">Complete</a>
          <?php else: ?>
            <?= ucfirst($status) ?>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include 'includes/footer.php'; ?>
