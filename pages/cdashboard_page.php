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
    <h1>LendEase - Client Loan Application System</h1>
  </header>

  <div class="main-container">
    <!-- Sidebar Navigation -->
    <div class="sidebar">
      <a href="../pages/cdashboard_page.php" class="active">Dashboard</a>
      <a href="../pages/capply_loan_page.php">Apply for a Loan</a>
      <a href="../pages/cloandetails_page.php">Loan Details</a>
      <a href="../pages/cprofile_page.php">Profile</a>
      <a href="../auth/logout.php">Logout</a>
    </div>

    <!-- Dashboard Content -->
    <div class="content">
      <h1>WELCOME, <span><?= htmlspecialchars($_SESSION['username']) ?>!</span></h1>
      <p>Here is the background about our loan application system.</p>

      <img src="../assets/images/lendease_black.png" alt="LendEase Logo" class="logo" />

      <h2 class="loan-name">LendEase</h2>
      <h3 class="tagline">"Empowering Your Financial Future, One Loan at a Time."</h3>

      <h2 class="about-us">About Us</h2>
      <p class="description">
        At LendEase, we aim to simplify and modernize the loan process by providing a secure, efficient, and user-friendly digital platform for both borrowers and lenders. Our system streamlines loan applications, approvals, tracking, and repayments, ensuring transparency and convenience every step of the way. Whether for personal, emergency, or business needs, LendEase is designed to make financial access faster, smarter, and more reliable for everyone.
      </p>

      <h2 class="goals">Goal:</h2>
      <p class="goal-description">
        1. We simplify the loan application and approval workflow through automation.<br>
        2. We reduce manual errors and improve data accuracy in loan records.<br>
        3. We provide real-time loan status updates and repayment tracking.<br>
        4. We enhance user experience through a responsive and intuitive interface.<br>
        5. We promote financial literacy and responsible borrowing habits among users.
      </p>

      <h2 class="vision">Vision:</h2>
      <p class="vision-description">
        To be a leading digital solution in the field of financial technology, enabling transparent, fast, and fair loan transactions for individuals and institutions in need of financial support.
      </p>

      <h1 class="mission">Mission:</h1>
      <p class="mission-description">
        To provide a reliable, accessible, and efficient digital platform that simplifies loan management processes, empowering both lenders and borrowers through technology-driven financial solutions.
      </p>

      <h2 class="loan-status">Overall Status</h2>
      <!-- Loan Summary Section -->
      <div class="summary">
        <div class="card">
          <h2>Active Loans</h2>
          <p>10000+</p> 
        </div>
        <div class="card">
          <h2>Completed Loans</h2>
          <p>99999+</p> 
        </div>
      </div>

      <h2 class="contact-us">Contact Us</h2>
      <p>lendease.corp@gmail.com</p>
      <p>lendease.corp@outlook.com </p>
      <p>+63 912 3456 789</p>
      <p>LendEase</p>
    </div>
  </div>

</body>
</html>