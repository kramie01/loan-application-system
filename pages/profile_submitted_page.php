<?php

session_start();
if (!isset($_SESSION['email'])) {
  header('Location: ../pages/home_page.php');
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Submitted Page</title>
    <link rel="stylesheet" href="../assets/css/home_style.css">
</head>

<body style="background: #fff;">

<div class="modal-content success">
  <h3>CONGRATS!</h3>
  <p>Your profile has been successfully completed. <br> Please log in again to access your account.</p>
  <button onclick="window.location.href='../pages/home_page.php'">Login Again</button>
</div>

</body>
</html>