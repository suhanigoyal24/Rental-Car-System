<?php 
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agency'){
    header("Location: ../auth/login.php");
    exit;
}

include("../config/db.php");

$agency_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM cars WHERE agency_id = ?");
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$result = $stmt->get_result();
$agency_stmt = $conn->prepare("SELECT agency_name FROM agency_details WHERE user_id=?");
$agency_stmt->bind_param("i", $agency_id);
$agency_stmt->execute();
$agency_res = $agency_stmt->get_result();
$agency = $agency_res->fetch_assoc();
$agency_name = $agency['agency_name'] ?? "Agency";
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Cars</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .top-bar { display:flex; justify-content:space-between; align-items:center; padding:20px 40px; background:white; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
        .dashboard-container { padding: 40px; }
        .card { border-radius: 15px; transition: 0.3s; }
        .card:hover { transform: scale(1.02); }
        .card img { height: 200px; object-fit: cover; border-top-left-radius: 15px; border-top-right-radius: 15px; }
        .empty-state { text-align: center; margin-top: 80px; }
        .empty-state img { width: 120px; margin-bottom: 20px; }
        .btn-back { margin-top: 20px; }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <h4>Agency Panel 🏢 - <?= htmlspecialchars($agency_name) ?></h4>
    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
</div>

<div class="dashboard-container">

    <h2 class="mb-4">🚗 My Cars</h2>

    <?php if($result->num_rows == 0): ?>
        <div class="empty-state">
            <img src="https://cdn-icons-png.flaticon.com/512/741/741407.png">
            <h4>No Cars Added Yet</h4>
            <p class="text-muted">Start adding cars to build your fleet.</p>
            <a href="add_car.php" class="btn btn-primary mt-3">Add Your First Car</a>
            <a href="dashboard.php" class="btn btn-secondary mt-2 btn-back">⬅ Back to Dashboard</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php while($car = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70" 
                             class="card-img-top">

                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($car['vehicle_model']); ?></h5>
                            <p class="card-text">
                                <strong>Number:</strong> <?= htmlspecialchars($car['vehicle_number']); ?><br>
                                <strong>Seats:</strong> <?= htmlspecialchars($car['seating_capacity']); ?><br>
                                <strong>Rent/Day:</strong> ₹<?= htmlspecialchars($car['rent_per_day']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <a href="dashboard.php" class="btn btn-secondary mt-3 btn-back">⬅ Back to Dashboard</a>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
