<?php
session_start();
include("../config/db.php");

// Only allow customers
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer'){
    header("Location: ../auth/login.php");
    exit;
}

$customer_id = $_SESSION['user_id'];
$car_id = $_GET['id'] ?? null;

if(!$car_id) die("Car not specified.");

// Fetch car info
$stmt = $conn->prepare("SELECT * FROM cars WHERE id=?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$car = $stmt->get_result()->fetch_assoc();

if(!$car) die("Car not found.");

// Fetch customer info
$stmt2 = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt2->bind_param("i", $customer_id);
$stmt2->execute();
$customer = $stmt2->get_result()->fetch_assoc();

// Fetch last 3 bookings for this car
$stmt3 = $conn->prepare("SELECT * FROM bookings WHERE car_id=? ORDER BY booking_date DESC LIMIT 3");
$stmt3->bind_param("i", $car_id);
$stmt3->execute();
$recent_bookings = $stmt3->get_result();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $booking_date = $_POST['booking_date'] ?? null;
    $return_date = $_POST['return_date'] ?? null;

    if(!$booking_date || !$return_date){
        die("Please select valid start and end dates.");
    }

    // Calculate total amount
    $days = (strtotime($return_date) - strtotime($booking_date)) / (60*60*24) + 1;
    $total_amount = $days * $car['rent_per_day'];

    // Insert booking
    $stmt4 = $conn->prepare("
        INSERT INTO bookings 
        (car_id, user_id, customer_name, customer_phone, booking_date, return_date, total_amount, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $status = 'pending';
    $stmt4->bind_param(
        "iissssds",
        $car_id,
        $customer_id,
        $name,
        $phone,
        $booking_date,
        $return_date,
        $total_amount,
        $status
    );
    $stmt4->execute();

    // Redirect to My Bookings
    header("Location: my_bookings.php?msg=Booking Successful!");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card img { height:200px; object-fit:cover; border-top-left-radius:15px; border-top-right-radius:15px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <div class="card mb-4">
        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70" class="card-img-top" alt="Car Image">
        <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($car['vehicle_model']); ?> (<?= $car['vehicle_number']; ?>)</h3>
            <p class="card-text">
                <strong>Seats:</strong> <?= htmlspecialchars($car['seating_capacity']); ?><br>
                <strong>Rent per Day:</strong> ₹<?= number_format($car['rent_per_day'],2); ?><br>
                <strong>Status:</strong> <?= ucfirst($car['status']); ?>
            </p>
        </div>
    </div>

    <?php if($recent_bookings->num_rows > 0): ?>
        <h5>Recent Bookings for this Car:</h5>
        <div class="list-group mb-4">
            <?php while($b = $recent_bookings->fetch_assoc()): ?>
                <div class="list-group-item">
                    <strong><?= htmlspecialchars($b['customer_name']); ?></strong> (<?= htmlspecialchars($b['customer_phone']); ?>)
                    <br>
                    <?= $b['booking_date']; ?> → <?= $b['return_date'] ?? 'N/A'; ?>
                    <br>
                    Total: ₹<?= number_format($b['total_amount'],2); ?> | Status: <?= ucfirst($b['status']); ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <h4>Book This Car</h4>
    <form method="POST">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($customer['name']); ?>">
        </div>
        <div class="mb-3">
            <label>Phone Number</label>
            <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($customer['phone']); ?>">
        </div>
        <div class="mb-3">
            <label>Start Date</label>
            <input type="date" name="booking_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>End Date</label>
            <input type="date" name="return_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Book Now</button>
    </form>
</div>
</body>
</html>
