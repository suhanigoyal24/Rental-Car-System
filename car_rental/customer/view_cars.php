<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer'){
    header("Location: ../auth/login.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM cars WHERE status='available'");
$stmt->execute();
$cars = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Cars</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .container { max-width: 1100px; margin: 50px auto; }
        .card { border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s ease; }
        .card:hover { transform: translateY(-5px); }
        .card img { width: 100%; height: 200px; object-fit: cover; border-top-left-radius: 15px; border-top-right-radius: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-center">🚗 Available Cars</h2>

    <?php if($cars->num_rows == 0): ?>
        <p class="text-center">No cars available at the moment.</p>
    <?php else: ?>
        <div class="row g-4">
            <?php while($car = $cars->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70" class="card-img-top" alt="Car Image">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= htmlspecialchars($car['vehicle_model']); ?></h5>
                            <p class="card-text">
                                Number: <?= htmlspecialchars($car['vehicle_number']); ?><br>
                                Seats: <?= htmlspecialchars($car['seating_capacity']); ?><br>
                                Rent/Day: ₹<?= number_format($car['rent_per_day'], 2); ?>
                            </p>
                            <a href="book_car.php?id=<?= $car['id']; ?>" class="btn btn-success">Book Now</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
