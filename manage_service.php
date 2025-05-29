<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$flash = "";

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Service WHERE Service_ID = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $flash = "Service deleted successfully!";
    } else {
        $flash = "Error deleting service: " . $stmt->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name = trim($_POST['service_name'] ?? '');
    $cost = floatval($_POST['cost'] ?? 0);
    $category = trim($_POST['category'] ?? '');

    if (!empty($service_name) && $cost > 0 && !empty($category)) {
        $stmt = $conn->prepare("INSERT INTO Service (Service_Name, Cost, Category) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $service_name, $cost, $category);
        if ($stmt->execute()) {
            $flash = "Service added successfully!";
        } else {
            $flash = "Error: " . $stmt->error;
        }
    } else {
        $flash = "Please enter all fields correctly.";
    }
}

$services = [];
$result = $conn->query("SELECT * FROM Service ORDER BY Category ASC, Service_ID DESC");
while ($row = $result->fetch_assoc()) {
    $services[$row['Category']][] = $row;
}
?>

<?php include 'includes/header.php'; ?>

<div class="ms-container">
    <h3 class="mb-4 text-center">Manage Services</h3>

    <?php if (!empty($flash)): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <h5 class="mb-4 text-center">Add New Service</h5>
            <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label">Service Name</label>
                    <input type="text" name="service_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cost (₱)</label>
                    <input type="number" step="0.01" name="cost" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select category</option>
                        <option value="Hair">Hair</option>
                        <option value="Nail">Nail</option>
                        <option value="Skincare">Skincare</option>
                        <option value="Makeup">Makeup</option>
                        <option value="Eyelash and Brow">Eyelash and Brow</option>
                        <option value="Grooming and Other">Grooming and Other</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add Service</button>
            </form>
        </div>

        <div class="col-md-6">
            <h5>Existing Services</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <th>Cost (₱)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($services)): ?>
                        <tr><td colspan="4" class="text-center">No services found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($services as $category => $cat_services): ?>
                            <tr class="table-secondary">
                                <td colspan="4"><strong><?= htmlspecialchars($category) ?> Services</strong></td>
                            </tr>
                            <?php foreach ($cat_services as $row): ?>
                                <tr>
                                    <td><?= $row['Service_ID'] ?></td>
                                    <td><?= htmlspecialchars($row['Service_Name']) ?></td>
                                    <td><?= number_format($row['Cost'], 2) ?></td>
                                    <td>
                                        <a href="?delete=<?= $row['Service_ID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this service?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
