<?php
session_start();
require_once "../config/db.php";

// 🔒 Agency Only
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agency'){
    header("Location: ../auth/login.php");
    exit;
}

$agency_id = $_SESSION['user_id'];

// Get car id
if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit;
}

$car_id = $_GET['id'];

// Fetch car (only if belongs to this agency)
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ? AND agency_id = ?");
$stmt->bind_param("ii", $car_id, $agency_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("Car not found or access denied.");
}

$car = $result->fetch_assoc();

// Update Logic
if(isset($_POST['update_car'])){

    $model = trim($_POST['vehicle_model']);
    $number = trim($_POST['vehicle_number']);
    $seats = trim($_POST['seating_capacity']);
    $rent = trim($_POST['rent_per_day']);

    if(empty($model) || empty($number) || empty($seats) || empty($rent)){
        $error = "All fields are required!";
    } else {

        $update = $conn->prepare("UPDATE cars 
            SET vehicle_model = ?, 
                vehicle_number = ?, 
                seating_capacity = ?, 
                rent_per_day = ?
            WHERE id = ? AND agency_id = ?");

        $update->bind_param("ssidii", 
            $model, 
            $number, 
            $seats, 
            $rent, 
            $car_id, 
            $agency_id
        );

        if($update->execute()){
            header("Location: dashboard.php?updated=1");
            exit;
        } else {
            $error = "Update failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Car</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container mt-4">
    <div class="card p-4 shadow-sm">
        <h3>Edit Car</h3>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="vehicle_model" class="form-control mb-2"
                   value="<?= $car['vehicle_model'] ?>" required>

            <input type="text" name="vehicle_number" class="form-control mb-2"
                   value="<?= $car['vehicle_number'] ?>" required>

            <input type="number" name="seating_capacity" class="form-control mb-2"
                   value="<?= $car['seating_capacity'] ?>" required>

            <input type="number" step="0.01" name="rent_per_day" class="form-control mb-2"
                   value="<?= $car['rent_per_day'] ?>" required>

            <button type="submit" name="update_car" class="btn btn-primary">
                Update Car
            </button>

            <a href="dashboard.php" class="btn btn-secondary ms-2">
                Back
            </a>
        </form>
    </div>
</div>

</body>
</html>
