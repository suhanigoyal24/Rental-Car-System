<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer'){
    header("Location: ../auth/login.php");
    exit;
}

$customer_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT b.*, c.vehicle_model, c.vehicle_number 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .container { max-width: 1000px; margin: 50px auto; }
        .card { border-radius: 15px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s ease; }
        .card:hover { transform: translateY(-5px); }
        .card img { width: 100%; height: 200px; object-fit: cover; }
        .empty-state { text-align: center; margin-top: 100px; }
        .empty-state img { width: 150px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
    <h2 class="text-center mb-4">📝 My Bookings</h2>
</div>

<div class="container">
    <?php if($bookings->num_rows == 0): ?>
        <div class="empty-state">
            <img src="https://cdn-icons-png.flaticon.com/512/744/744922.png" alt="No Bookings">
            <h4>You have not booked any cars yet!</h4>
            <p class="text-muted">Check out the cars we offer and make your first booking.</p>
            <a href="view_cars.php" class="btn btn-primary mt-3">View Available Cars</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php while($booking = $bookings->fetch_assoc()): ?>
                <div class="col-md-6">
                    <div class="card">
                        <img src="https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?auto=format&fit=crop&w=800&q=80" alt="Booking Image">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= $booking['vehicle_model']; ?> (<?= $booking['vehicle_number']; ?>)</h5>
                            <p class="card-text">
                                <strong>Booking Dates:</strong> <?= $booking['booking_date']; ?> to <?= $booking['return_date'] ?? 'N/A'; ?><br>
                                <strong>Total Amount:</strong> ₹<?= number_format($booking['total_amount'], 2); ?><br>
                                <strong>Status:</strong> <?= ucfirst($booking['status'] ?? 'pending'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
