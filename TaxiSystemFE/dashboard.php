<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "db.php";
include "header.php";
?>

<hr>
<br><br>
<h2>Welcome, <?php echo $_SESSION['user_name']; ?></h2>
<div style="text-align: right;">
    <a href="help.php" class="btn">Help</a>
</div>
<br><br>
<hr>

<h3>Book a Ride</h3>

<form action="book.php" method="POST">

    <label>From:</label><br>
    <select name="pickup" required>
        <?php
        $loc = $conn->query("SELECT * FROM locations");
        while ($row = $loc->fetch_assoc()) {
            echo "<option value='{$row['location_id']}'>{$row['name']}</option>";
        }
        ?>
    </select><br><br>

    <label>To:</label><br>
    <select name="drop" required>
        <?php
        $loc2 = $conn->query("SELECT * FROM locations");
        while ($row = $loc2->fetch_assoc()) {
            echo "<option value='{$row['location_id']}'>{$row['name']}</option>";
        }
        ?>
    </select><br><br>

    <button class="btn">Book Taxi</button>
</form>
<br><br>
    <a href="ride.php" class="btn">See current ride</a>
<br><br>    
<hr>

<h3>Available Taxis</h3>

<table>
<tr>
    <th>ID</th>
    <th>Driver</th>
    <th>Plate</th>
</tr>

<?php
$available = $conn->query("SELECT * FROM taxis WHERE status='Available'");
while ($t = $available->fetch_assoc()) {
    echo "<tr>
            <td>{$t['taxi_id']}</td>
            <td>{$t['driver_name']}</td>
            <td>{$t['plate_number']}</td>
          </tr>";
}
?>
</table>

<h3>Busy Taxis (On Ride)</h3>

<table>
<tr>
    <th>ID</th>
    <th>Driver</th>
    <th>Plate</th>
</tr>

<?php
$busy = $conn->query("SELECT * FROM taxis WHERE status='Busy'");
while ($t = $busy->fetch_assoc()) {
    echo "<tr>
            <td>{$t['taxi_id']}</td>
            <td>{$t['driver_name']}</td>
            <td>{$t['plate_number']}</td>
          </tr>";
}
?>
</table>

<br>
<hr>
<br><br>

<a href="records.php" class="btn">View Ride history</a>

<br><br>
<hr>
<br>
<a href="logout.php" class="btn">Logout</a>


