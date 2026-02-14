<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agency'){
    header("Location: ../auth/login.php");
    exit;
}

$agency_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Fetch existing agency details
$stmt_check = $conn->prepare("SELECT * FROM agency_details WHERE user_id=?");
$stmt_check->bind_param("i", $agency_id);
$stmt_check->execute();
$res_check = $stmt_check->get_result();
$agency = $res_check->fetch_assoc();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $agency_name = trim($_POST['agency_name']);
    $owner_name  = trim($_POST['owner_name']);
    $address     = trim($_POST['address']);
    $pan_number  = trim($_POST['pan_number']);
    $drc_number  = trim($_POST['drc_number']);

    // Validation
    if(empty($agency_name) || empty($owner_name) || empty($address) || empty($pan_number) || empty($drc_number)){
        $error = "All fields including PAN & e-RC numbers are required!";
    } else {
        if($agency){ // Update existing
            $stmt = $conn->prepare("UPDATE agency_details SET agency_name=?, owner_name=?, address=?, pan_number=?, drc_number=? WHERE user_id=?");
            $stmt->bind_param("sssssi", $agency_name, $owner_name, $address, $pan_number, $drc_number, $agency_id);
            if($stmt->execute()) $success = "Agency details updated successfully!";
            else $error = "Error updating: ".$conn->error;
        } else { // Insert new (unlikely, but safe)
            $stmt = $conn->prepare("INSERT INTO agency_details (user_id, agency_name, owner_name, address, pan_number, drc_number) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $agency_id, $agency_name, $owner_name, $address, $pan_number, $drc_number);
            if($stmt->execute()) $success = "Agency details saved successfully!";
            else $error = "Error inserting: ".$conn->error;
        }

        // Refresh agency data
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();
        $agency = $res_check->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complete Agency Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .card { border-radius:15px; max-width:600px; margin:auto; margin-top:50px; padding:30px; }
        .form-control { border-radius:10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow-sm">
        <h3 class="mb-4 text-center">Complete Your Agency Profile</h3>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Agency Name <span class="text-danger">*</span></label>
                <input type="text" name="agency_name" class="form-control" required
                       value="<?= htmlspecialchars($_POST['agency_name'] ?? $agency['agency_name'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label>Owner / Contact Person <span class="text-danger">*</span></label>
                <input type="text" name="owner_name" class="form-control" required
                       value="<?= htmlspecialchars($_POST['owner_name'] ?? $agency['owner_name'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label>Business Address <span class="text-danger">*</span></label>
                <input type="text" name="address" class="form-control" required
                       value="<?= htmlspecialchars($_POST['address'] ?? $agency['address'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label>PAN Card Number <span class="text-danger">*</span></label>
                <input type="text" name="pan_number" class="form-control" required
                       value="<?= htmlspecialchars($_POST['pan_number'] ?? $agency['pan_number'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label>Digital Registration Certificate (e-RC) Number <span class="text-danger">*</span></label>
                <input type="text" name="drc_number" class="form-control" required
                       value="<?= htmlspecialchars($_POST['drc_number'] ?? $agency['drc_number'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn btn-primary w-100">Save Details</button>
        </form>

        <div class="mt-3 text-center">
            <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
