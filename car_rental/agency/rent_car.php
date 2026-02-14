<?php
session_start();
require_once "config/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit;
}

if($_SESSION['role'] != 'customer'){
    die("Agencies cannot book cars.");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $customer_id = $_SESSION['user_id'];
    $car_id = $_POST['car_id'];
    $start_date = $_POST['start_date'];
    $days = $_POST['days'];

    // Calculate end date
    $end_date = date('Y-m-d', strtotime($start_date . " + $days days"));

    // 🔍 CHECK IF CAR ALREADY BOOKED IN SAME PERIOD
    $check = $conn->prepare("
        SELECT id FROM bookings 
        WHERE car_id = ?
        AND (
            (start_date <= ? AND DATE_ADD(start_date, INTERVAL number_of_days DAY) > ?)
        )
    ");

    $check->bind_param("iss", $car_id, $start_date, $start_date);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){
        die("Car is already booked for selected date.");
    }

    // Get rent per day
    $stmt = $conn->prepare("SELECT rent_per_day FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();

    $total_amount = $car['rent_per_day'] * $days;

    // Insert booking
    $insert = $conn->prepare("INSERT INTO bookings 
        (car_id, customer_id, start_date, number_of_days, total_amount)
        VALUES (?, ?, ?, ?, ?)");

    $insert->bind_param("iisid",
        $car_id,
        $customer_id,
        $start_date,
        $days,
        $total_amount
    );

    if($insert->execute()){
        header("Location: available_cars.php?booked=1");
        exit;
    } else {
        die("Booking failed.");
    }
}
?>
