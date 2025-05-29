<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: index.php");
    exit;
}

$customer_id = $_SESSION['user_id'];
$flash = "";

$services_by_category = [];
$result = $conn->query("SELECT Service_ID, Service_Name, Cost, Category FROM Service ORDER BY Category ASC, Service_Name ASC");
while ($row = $result->fetch_assoc()) {
    $services_by_category[$row['Category']][] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id   = $_POST['service_id'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';
    $remark       = $_POST['remark'] ?? '';
    $status       = "pending";

    $stmt = $conn->prepare("INSERT INTO Booking(Customer_ID, Booking_Date, Booking_Time, Status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $customer_id, $booking_date, $booking_time, $status);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO Booking_Details(Booking_ID, Customer_ID, Service_ID) VALUES (?, ?, ?)");
        $stmt2->bind_param("iii", $booking_id, $customer_id, $service_id);
        $stmt2->execute();

        $flash = "Booking successful!";
    } else {
        $flash = "Error: " . $stmt->error;
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8 booking-form-container">
      <h3 class="text-center mb-4">Book a Service</h3>    

      <?php if (!empty($flash)): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="mb-3">
          <label class="form-label">Select Service</label>
          <select name="service_id" class="form-select" required>
            <option disabled selected>-- Choose Service --</option>
            <?php foreach ($services_by_category as $category => $service_list): ?>
              <optgroup label="<?= htmlspecialchars($category) ?> Services">
                <?php foreach ($service_list as $s): ?>
                  <option value="<?= $s['Service_ID'] ?>">
                    <?= htmlspecialchars($s['Service_Name']) ?> (₱<?= number_format($s['Cost'], 2) ?>)
                  </option>
                <?php endforeach; ?>
              </optgroup>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Booking Date</label>
          <input type="date" name="booking_date" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Booking Time</label>
          <input type="time" name="booking_time" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Confirm Booking</button>
      </form>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
