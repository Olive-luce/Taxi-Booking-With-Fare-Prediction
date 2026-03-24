<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";
include "header.php"; 
?>

<h2>Your Ride History</h2>
<a href="dashboard.php" class="btn">Back to Dashboard</a>
<br><br>
<hr>
<table border="1" cellpadding="8" cellspacing="0">
<tr>
    <th>Pickup</th>
    <th>Drop</th>
    <th>Driver</th>          
    <th>License Plate</th>
    <th>Distance (km)</th>
    <th>Fare (BDT)</th>
    <th>Time</th>
    <th>Ride Status</th>
</tr>

<?php
$uid = $_SESSION['user_id'];

$history = $conn->query("
    SELECT l1.name AS pickup, l2.name AS drop_loc,t.driver_name,t.plate_number AS license_plate, b.distance_km, b.fare, b.booking_time,b.ride_status
    FROM bookings b
    JOIN locations l1 ON b.pickup_location_id = l1.location_id
    JOIN locations l2 ON b.drop_location_id = l2.location_id
    JOIN taxis t ON b.taxi_id = t.taxi_id
    WHERE b.user_id = $uid
    ORDER BY b.booking_time DESC
");

while ($r = $history->fetch_assoc()) {
    echo "<tr>
        <td>{$r['pickup']}</td>
        <td>{$r['drop_loc']}</td>
        <td>{$r['driver_name']}</td>
        <td>{$r['license_plate']}</td>
        <td>{$r['distance_km']}</td>
        <td>{$r['fare']}</td>
        <td>{$r['booking_time']}</td>
        <td>{$r['ride_status']}</td>
    </tr>";
}
?>
</table>
<hr>