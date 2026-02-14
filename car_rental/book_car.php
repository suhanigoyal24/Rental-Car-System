<?php
session_start();
include("config/db.php");

// Show errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Only customers can book
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'customer'){
    die("<div class='alert alert-danger'>Only customers can book cars!</div>");
}

// Check if POST request
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $car_id = intval($_POST['car_id']);
    $customer_id = $_SESSION['user_id'];
    $start_date = $_POST['start_date'];
    $days = intval($_POST['days']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);

    if(empty($name) || empty($phone) || empty($start_date) || $days <= 0){
        die("<div class='alert alert-danger'>Please fill all fields properly.</div>");
    }

    // Check if car exists
    $stmt = $conn->prepare("SELECT rent_per_day FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0){
        die("<div class='alert alert-danger'>Car not found!</div>");
    }

    $car = $result->fetch_assoc();
    $rent_per_day = $car['rent_per_day'];
    $total_amount = $rent_per_day * $days;

    // Check if already booked
    $check = $conn->prepare("SELECT id FROM bookings WHERE car_id = ?");
    $check->bind_param("i", $car_id);
    $check->execute();
    $check_result = $check->get_result();

    if($check_result->num_rows > 0){
        die("<div class='alert alert-danger'>Car is already booked!</div>");
    }

    // Insert booking
    $insert = $conn->prepare("INSERT INTO bookings 
        (car_id, customer_id, start_date, number_of_days, total_amount, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())");

    $insert->bind_param("iisid", $car_id, $customer_id, $start_date, $days, $total_amount);

    if($insert->execute()){
        echo "<div class='alert alert-success'>
                Booking Successful! <br>
                Total Amount: ₹$total_amount
              </div>";
    } else {
        echo "<div class='alert alert-danger'>
                Error: ".$insert->error."
              </div>";
    }
}
?>
