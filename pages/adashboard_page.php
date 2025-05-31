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
    <title>Admin Page</title>
    <link rel="stylesheet" href="../assets/css/home_style.css">
</head>

<body style="background: #fff;">

<div class="box">
  <h1>Welcome, <span><?= $_SESSION['username']; ?></span></h1>
  <p>This is an <span>admin</span> page</p>
  <button onclick="window.location.href='../auth/logout.php'">Logout</button>
</div>

</body>

</html>