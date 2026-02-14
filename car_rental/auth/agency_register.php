<?php
session_start();
require_once __DIR__ . '/../config/db.php'; // Update path as per your folder structure

$error = "";
$success = "";

if(isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $contact_person = trim($_POST['contact_person']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'agency';

    // 1️⃣ Validate required fields
    if(empty($name) || empty($contact_person) || empty($email) || empty($phone) || empty($address) || empty($password) || empty($confirm_password)) {
        $error = "Please fill all required fields!";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // 2️⃣ Check if email already exists
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        if($stmt_check->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // 3️⃣ Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // 4️⃣ Insert into users table
            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $name, $email, $password_hash, $role);
            if($stmt_insert->execute()) {
                $user_id = $stmt_insert->insert_id;

                // 5️⃣ Insert into agency_details
                $stmt_agency = $conn->prepare("INSERT INTO agency_details (user_id, agency_name, owner_name, address) VALUES (?, ?, ?, ?)");
                $stmt_agency->bind_param("isss", $user_id, $name, $contact_person, $address);
                $stmt_agency->execute();

                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
                // Clear POST data so the form is empty
                $_POST = [];
            } else {
                $error = "Database error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agency Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { margin:0; padding:0; font-family:'Segoe UI', sans-serif; background: url('https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed; background-size: cover; }
        body::before { content: ""; position: fixed; top:0; left:0; width:100%; height:100%; backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index:0; }
        .container { position: relative; z-index: 1; max-width:500px; margin-top:50px; }
        .card { border-radius:15px; padding:30px; background: rgba(255,255,255,0.9); box-shadow:0 4px 12px rgba(0,0,0,0.2); }
        .form-control { border-radius:10px; }
        .btn-primary { border-radius:10px; }
        .alert { border-radius:10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h3 class="text-center mb-4">Agency Registration</h3>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name / Agency Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Owner / Contact Person <span class="text-danger">*</span></label>
                <input type="text" name="contact_person" class="form-control" value="<?= htmlspecialchars($_POST['contact_person'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Business Address <span class="text-danger">*</span></label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <input type="hidden" name="role" value="agency">
            <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
        </form>

        <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>
</body>
</html>
