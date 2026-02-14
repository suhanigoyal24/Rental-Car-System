<?php
session_start();
include("config/db.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$cars_query = "SELECT * FROM cars";
$result = $conn->query($cars_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Cars</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<img src="https://source.unsplash.com/400x300/?car,<?= urlencode($car['vehicle_model']) ?>" class="card-img-top">

<div class="container mt-5">
    <h2 class="mb-4 text-center">Available Cars</h2>

    <div class="row">
    <?php
    if($result->num_rows == 0){
        echo "<p class='text-center'>No cars available right now. Please check later.</p>";
    } else {
        while($car = $result->fetch_assoc()){

            $check_booking = $conn->prepare("SELECT id FROM bookings WHERE car_id = ?");
            $check_booking->bind_param("i", $car['id']);
            $check_booking->execute();
            $booked_result = $check_booking->get_result();
            $is_booked = ($booked_result->num_rows > 0);
    ?>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm p-3 h-100">

                <h5 class="mb-3"><?= $car['vehicle_model'] ?></h5>

                <p>
                    <strong>Vehicle No:</strong> <?= $car['vehicle_number'] ?><br>
                    <strong>Seats:</strong> <?= $car['seating_capacity'] ?><br>
                    <strong>Rent:</strong> ₹<?= $car['rent_per_day'] ?>/day
                </p>

                <?php
                if(isset($_SESSION['role'])){

                    if($_SESSION['role'] == 'customer'){

                        if($is_booked){
                            echo '<p class="text-danger fw-bold">Already Booked</p>';
                        } else {
                ?>

                            <form class="rent-form mt-2" data-car-id="<?= $car['id'] ?>">
                                <div class="mb-2">
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <input type="number" name="days" min="1" value="1" class="form-control" required>
                                </div>
                                <button type="button" class="btn btn-primary w-100 open-booking-modal">
                                    Rent Car
                                </button>
                            </form>

                <?php
                        }

                    } else if($_SESSION['role'] == 'agency'){

                        echo $is_booked 
                            ? '<p class="text-danger fw-bold">Booked</p>' 
                            : '<p class="text-success fw-bold">Available</p>';
                    }

                } else {
                    echo '<p class="text-muted">Login as customer to rent cars.</p>';
                }
                ?>

            </div>
        </div>

    <?php
        }
    }
    ?>
    </div>
</div>
