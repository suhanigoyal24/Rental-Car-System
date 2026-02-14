<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer'){
    header("Location: ../auth/login.php");
    exit;
}

$customer_id = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    $error = "";
    $success = "";

    // Validation
    if(empty($name) || empty($email) || empty($phone)){
        $error = "Name, Email, and Phone are required!";
    } else {
        // Check if email is used by another customer
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email=? AND id<>?");
        $stmt_check->bind_param("si", $email, $customer_id);
        $stmt_check->execute();
        $stmt_check->store_result();
        if($stmt_check->num_rows > 0){
            $error = "Email already in use by another account!";
        } else {
            // Update query
            if(!empty($password)){
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, password=? WHERE id=?");
                $stmt->bind_param("ssssi", $name, $email, $phone, $password_hash, $customer_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
                $stmt->bind_param("sssi", $name, $email, $phone, $customer_id);
            }

            if($stmt->execute()){
                $success = "Profile updated successfully!";
                header("Location: dashboard.php?msg=".urlencode($success));
                exit;
            } else {
                $error = "Error updating profile: ".$conn->error;
            }
        }
    }
}
?>
