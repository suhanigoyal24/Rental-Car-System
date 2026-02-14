<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agency'){
    header("Location: ../auth/login.php");
    exit;
}

require '../config/db.php';
$agency_id = $_SESSION['user_id'];

// Handle booking status updates
if(isset($_GET['action'], $_GET['booking_id'])){
    $action = $_GET['action'];
    $booking_id = (int)$_GET['booking_id'];

    if(in_array($action, ['confirmed','cancelled'])){
        $stmt = $conn->prepare("UPDATE bookings b 
                                JOIN cars c ON b.car_id=c.id
                                SET b.status=? 
                                WHERE b.id=? AND c.agency_id=?");
        $stmt->bind_param("sii",$action,$booking_id,$agency_id);
        $stmt->execute();
        header("Location: booked_cars.php"); // refresh to avoid resubmission
        exit;
    }
}

// Fetch bookings for this agency
$sql = "
SELECT b.id AS booking_id, b.car_id, b.user_id, b.booking_date, b.return_date, b.status,
       c.vehicle_model, c.vehicle_number, c.rent_per_day,
       u.name AS customer_name, u.email AS customer_email
FROM bookings b
JOIN cars c ON b.car_id = c.id
JOIN users u ON b.user_id = u.id
WHERE c.agency_id = ?
ORDER BY b.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$result = $stmt->get_result();

// Quick summary
$summary_sql = "
SELECT b.status, COUNT(*) AS count
FROM bookings b
JOIN cars c ON b.car_id = c.id
WHERE c.agency_id = ?
GROUP BY b.status
";
$stmt_sum = $conn->prepare($summary_sql);
$stmt_sum->bind_param("i", $agency_id);
$stmt_sum->execute();
$sum_result = $stmt_sum->get_result();

$summary = ['pending'=>0,'confirmed'=>0,'completed'=>0,'cancelled'=>0];
while($row = $sum_result->fetch_assoc()){
    $summary[$row['status']] = $row['count'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booked Cars</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .card { border-radius: 12px; }
        .badge { font-size: 0.9em; }
        .summary-box { border-radius:12px; padding:15px; color:white; text-align:center; }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <h4>Agency Panel 🏢 - <?= htmlspecialchars($agency_name) ?></h4>
    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
</div>

<div class="container">
    <h3 class="mb-4">Bookings for Your Cars</h3>
    <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Back to Dashboard</a>

    <!-- Quick Summary -->
    <div class="row mb-4">
        <div class="col-md-3"><div class="summary-box bg-warning">Pending <br><?= $summary['pending'] ?></div></div>
        <div class="col-md-3"><div class="summary-box bg-success">Confirmed <br><?= $summary['confirmed'] ?></div></div>
        <div class="col-md-3"><div class="summary-box bg-primary">Completed <br><?= $summary['completed'] ?></div></div>
        <div class="col-md-3"><div class="summary-box bg-danger">Cancelled <br><?= $summary['cancelled'] ?></div></div>
    </div>

    <?php if($result->num_rows == 0): ?>
        <div class="alert alert-info">No bookings yet.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Car</th>
                    <th>Vehicle No.</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Booking Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['vehicle_model']) ?></td>
                    <td><?= htmlspecialchars($row['vehicle_number']) ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['customer_email']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td><?= htmlspecialchars($row['return_date']) ?></td>
                    <td>
                        <?php
                            switch($row['status']){
                                case 'pending': echo '<span class="badge bg-warning">Pending</span>'; break;
                                case 'confirmed': echo '<span class="badge bg-success">Confirmed</span>'; break;
                                case 'completed': echo '<span class="badge bg-primary">Completed</span>'; break;
                                case 'cancelled': echo '<span class="badge bg-danger">Cancelled</span>'; break;
                            }
                        ?>
                    </td>
                    <td>
                        <?php if($row['status']=='pending'): ?>
                            <a href="?action=confirmed&booking_id=<?= $row['booking_id'] ?>" class="btn btn-success btn-sm mb-1">Confirm</a>
                            <a href="?action=cancelled&booking_id=<?= $row['booking_id'] ?>" class="btn btn-danger btn-sm mb-1">Cancel</a>
                        <?php else: ?>
                            <span class="text-muted">No actions</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
