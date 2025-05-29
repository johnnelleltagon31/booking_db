<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? '';

    if ($user_type === 'customer') {
        $stmt = $conn->prepare("SELECT * FROM Customer WHERE Username=? LIMIT 1");
    } else {
        $stmt = $conn->prepare("SELECT * FROM Admin WHERE User_Name=? LIMIT 1");
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            $_SESSION['user_id'] = ($user_type === 'customer') ? $row['Customer_ID'] : $row['Admin_ID'];
            $_SESSION['user_type'] = $user_type;
            $redirect = ($user_type === 'customer') ? 'customer_dashboard.php' : 'admin_dashboard.php';
            header("Location: $redirect");
            exit;
        }
    }

    $_SESSION['flash'] = "Invalid credentials!";
    header("Location: login.php");
    exit;
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="login-container mx-auto">
        <h3 class="text-center mb-4">User Login</h3>

        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select name="user_type" class="form-select" required>
                    <option value="" disabled selected>---Select User Type---</option>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-100">Login</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
