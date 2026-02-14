<?php
session_start();
require_once "../config/db.php";

// 🔒 Access Control (Agency Only)
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agency'){
    header("Location: ../auth/login.php");
    exit;
}

$agency_id = $_SESSION['user_id'];

// 🔹 Ensure agency has completed their profile
$stmt_details = $conn->prepare("SELECT * FROM agency_details WHERE user_id=?");
$stmt_details->bind_param("i", $agency_id);
$stmt_details->execute();
$result_details = $stmt_details->get_result();

if($result_details->num_rows === 0){
    header("Location: fill_agency_details.php?msg=Please complete your agency profile before adding cars");
    exit;
}

$editing = false;
$error = "";
$success = "";

// Check if editing an existing car
if(isset($_GET['id'])){
    $editing = true;
    $car_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM cars WHERE id=? AND agency_id=?");
    $stmt->bind_param("ii", $car_id, $agency_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $car = $result->fetch_assoc();
    } else {
        $error = "Car not found!";
    }
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $model = trim($_POST['vehicle_model']);
    $number = trim($_POST['vehicle_number']);
    $seats = trim($_POST['seating_capacity']);
    $rent = trim($_POST['rent_per_day']);

    if(empty($model) || empty($number) || empty($seats) || empty($rent)){
        $error = "All fields are required!";
    } else {
        // Check unique vehicle number (exclude current car if editing)
        if($editing){
            $check = $conn->prepare("SELECT id FROM cars WHERE vehicle_number=? AND id != ?");
            $check->bind_param("si",$number,$car_id);
        } else {
            $check = $conn->prepare("SELECT id FROM cars WHERE vehicle_number=?");
            $check->bind_param("s",$number);
        }
        $check->execute();
        $check->store_result();
        if($check->num_rows > 0){
            $error = "Vehicle number already exists!";
        } else {
            if($editing){
                $stmt = $conn->prepare("UPDATE cars SET vehicle_model=?, vehicle_number=?, seating_capacity=?, rent_per_day=? WHERE id=? AND agency_id=?");
                $stmt->bind_param("ssiiii",$model,$number,$seats,$rent,$car_id,$agency_id);
                if($stmt->execute()){
                    $success = "Car details updated successfully!";
                } else {
                    $error = "Failed to update car!";
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO cars (agency_id, vehicle_model, vehicle_number, seating_capacity, rent_per_day) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issii",$agency_id,$model,$number,$seats,$rent);
                if($stmt->execute()){
                    $success = "New car added successfully!";
                    // Clear form values
                    $model = $number = $seats = $rent = "";
                } else {
                    $error = "Something went wrong!";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $editing ? "Edit Car" : "Add New Car"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <h4>Agency Panel 🏢 - <?= htmlspecialchars($agency_name) ?></h4>
    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
</div>

<div class="container mt-5">
    <div class="card p-4 shadow-sm mx-auto" style="max-width:600px;">
        <h3 class="mb-4"><?= $editing ? "Edit Car Details" : "Add New Car"; ?></h3>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="text" name="vehicle_model" class="form-control" placeholder="Vehicle Model" 
                       value="<?= $editing ? htmlspecialchars($car['vehicle_model']) : ($model ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <input type="text" name="vehicle_number" class="form-control" placeholder="Vehicle Number" 
                       value="<?= $editing ? htmlspecialchars($car['vehicle_number']) : ($number ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <input type="number" name="seating_capacity" class="form-control" placeholder="Seating Capacity" 
                       value="<?= $editing ? $car['seating_capacity'] : ($seats ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <input type="number" step="0.01" name="rent_per_day" class="form-control" placeholder="Rent Per Day (₹)" 
                       value="<?= $editing ? $car['rent_per_day'] : ($rent ?? ''); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <?= $editing ? "Update Car" : "Add Car"; ?>
            </button>
        </form>

        <div class="mt-3 text-center">
            <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
