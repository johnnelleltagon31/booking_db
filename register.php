<?php
session_start();
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $mobile   = $_POST['mobile'];       
    $gender   = $_POST['gender'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $status   = "active";

    $check = $conn->prepare("SELECT * FROM Customer WHERE Username = ? OR Email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['flash'] = "Username or email already taken!";
        header("Location: register.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO Customer (Name, Email, Mobile_Number, Gender, Created_Date, Username, Password, Status) 
                            VALUES (?, ?, ?, ?, CURDATE(), ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $mobile, $gender, $username, $password, $status);

    if ($stmt->execute()) {
        $_SESSION['flash'] = "Registration successful! Please login.";
    } else {
        $_SESSION['flash'] = "Registration failed: " . $stmt->error;
    }

    header("Location: register.php");
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
  <div class="register-container mx-auto">
    <h3 class="text-center mb-4">Customer Registration</h3>

    <?php if (isset($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center">
        <?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Mobile</label>
          <input type="tel" name="mobile" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-select" required>
            <option disabled selected>-- Select Gender --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
      </div>
      <button type="submit" class="btn btn-success w-100">Register</button>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
