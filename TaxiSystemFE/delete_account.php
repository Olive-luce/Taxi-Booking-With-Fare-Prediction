<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";

$uid = $_SESSION['user_id'];

$conn->query("
    UPDATE taxis t
    JOIN bookings b ON t.taxi_id = b.taxi_id
    SET t.status = 'Available'
    WHERE b.user_id = $uid
      AND b.ride_status = 'pending'
");

$conn->query("DELETE FROM bookings WHERE user_id = $uid");

$conn->query("DELETE FROM users WHERE user_id = $uid");


session_destroy();
header("Location: login.php");
exit;

