<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer'){
    header("Location: ../auth/login.php");
    exit;
}

include("../config/db.php");
$customer_id = $_SESSION['user_id'];

// Fetch customer profile details
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$customer_name = $customer['name'] ?? "Customer";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .top-bar { display:flex; justify-content:space-between; align-items:center; padding:20px 40px; background:white; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
        .dashboard-container { padding: 40px; }
        .card { border-radius: 15px; transition: 0.3s ease; cursor: pointer; }
        .card:hover { transform: scale(1.03); }
        .card img { height: 220px; object-fit: cover; border-top-left-radius: 15px; border-top-right-radius: 15px; width: 100%; }
    </style>
</head>
<body>

<div class="top-bar">
    <h4>Customer Panel 🚘</h4>
    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
</div>

<div class="dashboard-container">
    <div class="text-center mb-5">
        <h2>Welcome, <?= htmlspecialchars($customer_name) ?> 👋</h2>
        <p class="text-muted">Explore and book cars easily.</p>
    </div>

    <div class="row justify-content-center g-4">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card shadow" data-bs-toggle="modal" data-bs-target="#profileModal">
                <img src="https://images.pexels.com/photos/443383/pexels-photo-443383.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Profile Image">
                <div class="card-body text-center">
                    <h5>Profile Details</h5>
                    <p>Update your contact info and password.</p>
                    <button class="btn btn-info mt-2" data-bs-toggle="modal" data-bs-target="#profileModal">Edit Profile</button>
                </div>
            </div>
        </div>

        <!-- Browse Cars Card -->
        <div class="col-md-4">
            <div class="card shadow" onclick="window.location='view_cars.php'">
                <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70" alt="Browse Cars">
                <div class="card-body text-center">
                    <h5>Browse Cars</h5>
                    <p>Find the perfect car for your journey.</p>
                    <a href="view_cars.php" class="btn btn-primary mt-2">View Cars</a>
                </div>
            </div>
        </div>

        <!-- My Bookings Card -->
        <div class="col-md-4">
            <div class="card shadow" onclick="window.location='my_bookings.php'">
                <img src="https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?auto=format&fit=crop&w=800&q=80" alt="My Bookings">
                <div class="card-body text-center">
                    <h5>Your Bookings</h5>
                    <p>Track and manage your rentals.</p>
                    <a href="my_bookings.php" class="btn btn-success mt-2">My Bookings</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="customer_profile_update.php">
        <div class="modal-header">
          <h5 class="modal-title" id="profileModalLabel">Update Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label>Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($customer['name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($customer['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Phone <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($customer['phone'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter new password if changing">
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
