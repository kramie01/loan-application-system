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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Client - Dashboard</title>
  <link rel="stylesheet" href="../assets/css/cdashboard_style.css" />
</head>

<body>
  <header>
    <img src="../assets/images/lendease_white.png" alt="Loan Logo" />
    <h1>Cashalo - Loan Application System</h1>
  </header>

  <div class="main-container">
    <!-- Sidebar Navigation -->
    <div class="sidebar">
      <a href="../pages/cdashboard_page.php">Dashboard</a>
      <a href="../pages/capply_loan_page.php">Apply for a Loan</a>
      <a href="../pages/cdetails_page.php">View Loan Details</a>
      <a href="../pages/cprofile_page.php">Profile</a>
      <a href="../auth/logout.php">Logout</a>
    </div>

    <!-- Dashboard Content -->
    <div class="content">
      <h1>Welcome, <span><?= htmlspecialchars($_SESSION['username']) ?>!</span></h1>
      <p>Here is your loan overview and quick access to features.</p>

      <!-- Loan Summary Section -->
      <div class="loan-summary">
        <div class="card">
          <h2>Active Loans</h2>
          <p>0</p> 
        </div>
        <div class="card">
          <h2>Pending Applications</h2>
          <p>0</p>
        </div>
        <div class="card">
          <h2>Completed Loans</h2>
          <p>0</p> 
        </div>
      </div>
    </div>
  </div>
</body>
</html>