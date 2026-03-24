<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "header.php";
?>

<h2>Help & Account Settings</h2>
<a href="dashboard.php" class="btn">Back to Dashboard</a>

<hr>

<p>If you are facing issues or want to manage your account, you can use the options below.</p>

<h3>Delete Account</h3>

<p style="color:red;">
    Warning: Deleting your account is permanent and cannot be undone.
</p>

<form action="delete_account.php" method="post"
      onsubmit="return confirm('Are you absolutely sure? This action cannot be undone.');">
    <button class="btn">Delete My Account</button>
</form>

<hr>

<h3>Need Help?</h3>
<p>
    For support, please contact the administrator or check the user guide. 
    Contact: olive-taxi_system@nomnom.com
</p>

<br>
<a href="logout.php" class="btn">Logout</a>
