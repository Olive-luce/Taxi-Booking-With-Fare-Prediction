<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";
include "header.php"; 

$uid = $_SESSION['user_id'];

// Handle ride status updates
if (isset($_POST['booking_id'], $_POST['status'])) {

    $booking_id = (int)$_POST['booking_id'];
    $status     = $_POST['status'];

    if ($status === 'completed' || $status === 'cancelled') {

        $conn->begin_transaction();

        try {
            // 1️⃣ Update booking status (only if belongs to this user and is pending)
            $stmt = $conn->prepare("
                UPDATE bookings 
                SET ride_status = ?
                WHERE booking_id = ? AND user_id = ? AND ride_status = 'pending'
            ");
            $stmt->bind_param("sii", $status, $booking_id, $uid);
            $stmt->execute();

            // 2️⃣ Free taxi if booking was updated
            if ($stmt->affected_rows > 0) {
                $stmt2 = $conn->prepare("
                    UPDATE taxis t
                    JOIN bookings b ON t.taxi_id = b.taxi_id
                    SET t.status = 'Available'
                    WHERE b.booking_id = ? AND b.user_id = ?
                ");
                $stmt2->bind_param("ii", $booking_id, $uid);
                $stmt2->execute();
                $stmt2->close();
            }

            $stmt->close();
            $conn->commit();

        } catch (Exception $e) {
            $conn->rollback();
            die("Error updating ride: " . $e->getMessage());
        }
    }
}
?>

<h2>Your Current Ride</h2>
<a href="dashboard.php" class="btn">Back to Dashboard</a>
<br><br>
<hr>

<table border="1" cellpadding="8" cellspacing="0">
<tr>
    <th>Pickup</th>
    <th>Drop</th>
    <th>Driver</th>
    <th>Plate</th>
    <th>Distance (km)</th>
    <th>Fare (BDT)</th>
    <th>Time</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php
$current = $conn->prepare("
    SELECT b.booking_id,
           l1.name AS pickup,
           l2.name AS drop_loc,
           t.driver_name,
           t.plate_number,
           b.distance_km,
           b.fare,
           b.booking_time,
           b.ride_status
    FROM bookings b
    JOIN locations l1 ON b.pickup_location_id = l1.location_id
    JOIN locations l2 ON b.drop_location_id = l2.location_id
    JOIN taxis t ON b.taxi_id = t.taxi_id
    WHERE b.user_id = ? AND b.ride_status = 'pending'
    ORDER BY b.booking_time DESC
");

$current->bind_param("i", $uid);
$current->execute();
$result = $current->get_result();

if ($result->num_rows === 0) {
    echo "<tr><td colspan='9'>No active ride</td></tr>";
} else {
    while ($r = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$r['pickup']}</td>
            <td>{$r['drop_loc']}</td>
            <td>{$r['driver_name']}</td>
            <td>{$r['plate_number']}</td>
            <td>{$r['distance_km']}</td>
            <td>{$r['fare']}</td>
            <td>{$r['booking_time']}</td>
            <td>{$r['ride_status']}</td>
            <td>
                <form method='post' style='display:inline'>
                    <input type='hidden' name='booking_id' value='{$r['booking_id']}'>
                    <button class='btn' name='status' value='completed'>Complete</button>
                </form>

                <form method='post' style='display:inline'>
                    <input type='hidden' name='booking_id' value='{$r['booking_id']}'>
                    <button class='btn' name='status' value='cancelled'>Cancel</button>
                </form>
            </td>
        </tr>";
    }
}

$current->close();
?>
</table>

<hr>
<br>
<a href="records.php" class="btn">View Ride History</a>
<br><br>
