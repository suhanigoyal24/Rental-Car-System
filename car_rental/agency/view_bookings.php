<?php
session_start();
include("../config/db.php");

// Only allow agencies
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agency'){
    header("Location: ../auth/login.php");
    exit;
}

$agency_id = $_SESSION['user_id'];

// Fetch all cars for this agency
$cars_stmt = $conn->prepare("SELECT * FROM cars WHERE agency_id = ?");
$cars_stmt->bind_param("i", $agency_id);
$cars_stmt->execute();
$cars_result = $cars_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Bookings - Your Cars</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            background: #f4f6f9;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #212529;
            padding: 20px;
            color: white;
        }

        .sidebar h4 {
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #343a40;
        }

        .dashboard-content {
            flex: 1;
            padding: 40px;
        }

        .card {
            border-radius: 15px;
            transition: 0.3s;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .empty-state {
            text-align: center;
            margin-top: 80px;
        }

        .empty-state img {
            width: 120px;
            margin-bottom: 20px;
        }

        .back-btn {
            margin-top: 20px;
        }

        table {
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background: #0d6efd;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }
    </style>
</head>

<body>

<!-- Top Bar -->
<div class="top-bar">
    <h4>Agency Panel 🏢 - <?= htmlspecialchars($agency_name) ?></h4>
    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
</div>

<div class="dashboard-container">

    <!-- Main Content -->
    <div class="dashboard-content">

        <h2 class="mb-4">🚗 View Bookings - Your Cars</h2>

        <?php if($cars_result->num_rows == 0): ?>

            <div class="empty-state">
                <img src="https://cdn-icons-png.flaticon.com/512/741/741407.png">
                <h4>No Cars Added Yet</h4>
                <p class="text-muted">Start adding cars to manage your fleet.</p>
                <a href="add_car.php" class="btn btn-primary mt-3">Add Your First Car</a>
                <a href="dashboard.php" class="btn btn-secondary mt-2 back-btn">⬅ Back to Dashboard</a>
            </div>

        <?php else: ?>

            <?php while($car = $cars_result->fetch_assoc()): ?>

                <div class="card shadow mb-4">
                    <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70" 
                         class="card-img-top" 
                         style="height:200px; object-fit:cover;">

                    <div class="card-body">
                        <h5 class="card-title"><?= $car['vehicle_model']; ?> - <?= $car['vehicle_number']; ?></h5>
                        <p class="card-text">
                            <strong>Seats:</strong> <?= $car['seating_capacity']; ?><br>
                            <strong>Rent/Day:</strong> ₹<?= $car['rent_per_day']; ?>
                        </p>

                        <?php
                        // Fetch bookings for this car
                        $booking_stmt = $conn->prepare("
                            SELECT b.*, u.name AS customer_name 
                            FROM bookings b 
                            JOIN users u ON b.customer_id = u.id 
                            WHERE b.car_id = ?
                        ");
                        $booking_stmt->bind_param("i", $car['id']);
                        $booking_stmt->execute();
                        $bookings = $booking_stmt->get_result();
                        ?>

                        <?php if($bookings->num_rows == 0): ?>
                            <p class="text-success">No bookings for this car yet.</p>
                        <?php else: ?>
                            <h6>Bookings:</h6>
                            <table>
                                <tr>
                                    <th>Customer</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Total Amount</th>
                                </tr>
                                <?php while($booking = $bookings->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $booking['customer_name']; ?></td>
                                        <td><?= $booking['start_date']; ?></td>
                                        <td><?= $booking['end_date']; ?></td>
                                        <td>₹<?= $booking['total_amount']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endwhile; ?>

            <a href="dashboard.php" class="btn btn-secondary mt-3 back-btn">⬅ Back to Dashboard</a>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
