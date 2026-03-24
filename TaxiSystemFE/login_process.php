<?php
session_start();
include "db.php";


$email = $_POST['email'] ?? '';
$pass  = $_POST['password'] ?? '';

if (empty($email) || empty($pass)) {
    die("Please enter email and password.");
}

$sql = "SELECT user_id, name, password FROM users WHERE email='$email' LIMIT 1";
$res = $conn->query($sql);

if ($res->num_rows === 1) {
    $row = $res->fetch_assoc();

    if (password_verify($pass, $row['password'])) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user_name'] = $row['name'];

        echo "Login successful!";
        header("Location: dashboard.php");

    } else {
        echo "Invalid password.";
    }
} else {
    echo "No account found with this email.";
}
?>
