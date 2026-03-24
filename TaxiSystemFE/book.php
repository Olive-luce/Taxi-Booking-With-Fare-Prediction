<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user = (int)$_SESSION['user_id'];
$pickup = (int)$_POST['pickup'];
$drop   = (int)$_POST['drop'];

if ($pickup === $drop) {
    die("Pickup and Drop location cannot be the same.");
}


$pickupData = $conn->query("SELECT distance_from_base_km FROM locations WHERE location_id = $pickup")->fetch_assoc();
$dropData   = $conn->query("SELECT distance_from_base_km FROM locations WHERE location_id = $drop")->fetch_assoc();

$distance = abs($pickupData['distance_from_base_km'] - $dropData['distance_from_base_km']);
$baseFare = 50;
$perKmRate = 20;
$fare = $baseFare + ($distance * $perKmRate);

try {

    $conn->begin_transaction();

    $taxiResult = $conn->query("SELECT taxi_id FROM taxis WHERE status='Available' ORDER BY taxi_id LIMIT 1 FOR UPDATE");

    if ($taxiResult->num_rows === 0) {
        throw new Exception("Sorry, no taxis are currently available.");
    }

    $taxi = $taxiResult->fetch_assoc()['taxi_id'];


    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, taxi_id, pickup_location_id, drop_location_id, distance_km, fare)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiidd", $user, $taxi, $pickup, $drop, $distance, $fare);
    $stmt->execute();


    $conn->query("UPDATE taxis SET status='Busy' WHERE taxi_id = $taxi");


    $conn->commit();


    $taxiInfo = $conn->query("SELECT driver_name, plate_number FROM taxis WHERE taxi_id = $taxi")->fetch_assoc();
    echo "<h3>Booking Successful!</h3>";
    echo "Driver: {$taxiInfo['driver_name']}<br>";
    echo "Taxi License Plate: {$taxiInfo['plate_number']}<br>";
    echo "Taxi ID: $taxi<br>";
    echo "Distance: $distance km<br>";
    echo "Fare: $fare BDT<br><br>";
    echo "<a href='dashboard.php'>Back to Dashboard</a>";

} catch (Exception $e) {
    $conn->rollback();
    die($e->getMessage());
}
?>
