<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"  rel="stylesheet">

    
    <style>
        body {
            background-image: url('b5.jfif');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            min-height: 100vh;
            margin: 0;
            padding-top: 30px;
        }

        .content-wrapper {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container, .register-container {
            background-color: rgba(255, 255, 255, 0.53);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.25);
            max-width: 500px;
            width: 100%;
            margin: auto;
            margin-top: 50px;
        }

        .login-container {
            max-width: 500px;
        }

        .register-container {
            max-width: 800px;
        }

        .service-container {
            background-color: rgba(255, 255, 255, 0.72); 
            padding: 30px;                                
            border-radius: 12px;                         
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);     
            max-width: 600px;                            
            margin: 40px auto;                            
        }

        h5 {
            margin-bottom: 1.5rem;                       
            text-align: center;                          
        }

        form .form-label {
            font-weight: 600;                            
        }

        .custom-navbar {
            font-size: 1.3rem; 
            box-shadow: 0 2px 8px rgba(255, 254, 254, 0.1);
        }

        .custom-navbar .nav-link {
            color: #000 !important;
            font-weight: 500;
            font-size: 1.2rem; 
        }

        .custom-navbar .navbar-brand {
            font-size: 1.5rem; 
            font-weight: 700;
            color: #000 !important;
        }

        .booking-form-container {
            background-color: rgba(255, 255, 255, 0.7);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            margin-bottom: 50px;
        }

        .form-label {
            font-weight: 600;
            font-size: 1rem;
        }

        .btn-success {
            font-size: 1.1rem;
            font-weight: 600;
        }

        h3 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #343a40;
        }

        .alert {
            font-size: 1rem;
            font-weight: 500;
        }
    
        .customer-dashboard { 
            max-width: 900px; 
            margin: auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
        }

        .header { 
            font-size: 28px; 
            font-weight: bold; 
            margin-bottom: 20px; 
        }

        .stats { 
            display: flex; 
            gap: 20px; 
            margin-bottom: 30px; 
        }

        .card { 
            flex: 1; 
            background:rgba(226, 232, 240, 0.76); 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center; 
        }

        .card-title { 
            font-size: 14px; 
            color: #555; }
        .card-value { 
            font-size: 24px; 
            font-weight: bold; 
            margin-top: 10px; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }

        th, td { 
            padding: 10px; 
            border: 1px solid #ccc; 
            text-align: center; 
        }

        th { 
            background-color:rgba(240, 240, 240, 0.72); 
        }

          .ms-container {
        background-color:rgba(255, 255, 255, 0.44);
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h3, h5 {
        font-weight: 600;
        color: #333;
    }

    .form-label {
        font-weight: 500;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .table {
        margin-top: 1rem;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .alert-info {
        background-color: #e7f3fe;
        color: #31708f;
        border-color: #bce8f1;
        font-weight: 500;
    }
    </style>  

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top custom-navbar">
  <div class="container">
    <a class="navbar-brand" href="index.php">Booking System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['user_type'])): ?>
          <?php if($_SESSION['user_type'] == 'customer'): ?>
            <li class="nav-item"><a class="nav-link" href="customer_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="book_service.php">Book Service</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_service.php">Manage Service</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>


<div class="content-wrapper">
  <div class="container">
    <?php if(isset($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center"><?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
    <?php endif; ?>
  </div>
</div> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
