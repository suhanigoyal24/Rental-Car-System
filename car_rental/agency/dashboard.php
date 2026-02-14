<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agency'){
    header("Location: ../auth/login.php");
    exit;
}

// DB connection
require '../config/db.php';
$agency_id = $_SESSION['user_id'];

// Fetch agency details
$stmt = $conn->prepare("SELECT * FROM agency_details WHERE user_id=?");
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$result = $stmt->get_result();
$agency = $result->fetch_assoc();
$agency_name = $agency['agency_name'] ?? "Agency";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agency Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .top-bar { display:flex; justify-content:space-between; align-items:center; padding:20px 40px; background:white; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
        .dashboard-container { padding: 40px; }
        .card { border-radius: 15px; transition: 0.3s ease; cursor: pointer; }
        .card:hover { transform: scale(1.03); }
        .card img { height: 220px; object-fit: cover; border-top-left-radius: 15px; border-top-right-radius: 15px; }
        .form-control { border-radius: 10px; }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <h4>Agency Panel 🏢</h4>
    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
</div>

<!-- Main Content -->
<div class="dashboard-container">
    <div class="text-center mb-5">
        <h2>Welcome, <?= htmlspecialchars($agency_name) ?> 👋</h2>
        <p class="text-muted">Manage your fleet and bookings efficiently.</p>
    </div>

    <div class="row justify-content-center">

        <!-- Agency Details -->
        <div class="col-md-5 mb-4">
            <div class="card shadow" onclick="window.location='agency_form_updates.php'">
                <img src="https://images.pexels.com/photos/443383/pexels-photo-443383.jpeg?auto=compress&cs=tinysrgb&w=800" 
                     class="card-img-top" style="height:200px; object-fit:cover;">
                <div class="card-body text-center">
                    <h5>Agency Details</h5>
                    <p>Update your contact and business information.</p>
                    <a href="agency_form_updates.php" class="btn btn-info mt-2">Edit Details</a>
                </div>
            </div>
        </div>

        <!-- Add New Car -->
        <div class="col-md-5 mb-4">
            <div class="card shadow" onclick="window.location='add_car.php'">
                <img src="https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=800&q=80">
                <div class="card-body text-center">
                    <h5>Add New Car</h5>
                    <p>Start adding cars to build your fleet.</p>
                    <a href="add_car.php" class="btn btn-primary mt-2">Add Car</a>
                </div>
            </div>
        </div>

        <!-- My Cars -->
        <div class="col-md-5 mb-4">
            <div class="card shadow" onclick="window.location='my_cars.php'">
                <img src="https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=800&q=80">
                <div class="card-body text-center">
                    <h5>My Cars</h5>
                    <p>View and edit all vehicles you own.</p>
                    <a href="my_cars.php" class="btn btn-success mt-2">My Cars</a>
                </div>
            </div>
        </div>

        <!-- Booked Cars -->
        <div class="col-md-5 mb-4">
            <div class="card shadow" onclick="window.location='booked_cars.php'">
                <img src="https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?auto=format&fit=crop&w=800&q=80">
                <div class="card-body text-center">
                    <h5>Booked Cars</h5>
                    <p>Track all current bookings and customer details.</p>
                    <a href="booked_cars.php" class="btn btn-warning mt-2">View Bookings</a>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
