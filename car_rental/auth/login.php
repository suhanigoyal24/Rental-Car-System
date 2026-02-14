<?php
session_start();
include("../config/db.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(empty($email) || empty($password)){
        $error = "Both fields are required!";
    } else {

        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password, $role);

        if($stmt->num_rows > 0){
            $stmt->fetch();

            if(password_verify($password, $hashed_password)){

                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = $role;

                if($role === "agency"){
                    header("Location: ../agency/dashboard.php");
                    exit();
                } elseif($role === "customer"){
                    header("Location: ../customer/dashboard.php");
                    exit();
                }

            } else {
                $error = "Incorrect password!";
            }

        } else {
            $error = "Email not registered!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Correct CSS Path -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="auth-bg">

    <div class="auth-card">
        <h3>Login</h3>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>

            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Login
            </button>

        </form>

        <p class="text-center mt-3">
    Don't have an account?
    <a href="customer_register.php">Register as Customer</a> |
    <a href="agency_register.php">Register as Agency</a>
</p>


    </div>

</div>

</body>
</html>
