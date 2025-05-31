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
  <title>Client - Loan Details</title>
  <link rel="stylesheet" href="../assets/css/capply_style.css" />
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
      <a href="../pages/capply_page.php">Apply for a Loan</a>
      <a href="../pages/cdetails_page.php">View Loan Details</a>
      <a href="../pages/cprofile_page.php">Profile</a>
      <a href="../auth/logout.php">Logout</a>
    </div>

<section>
  <h3>Loan Information</h3>
  <?php if (isset($loan) && $loan): ?>
    <p><strong>Loan Amount:</strong> <?= htmlspecialchars($loan['loanAmount']) ?></p>
    <p><strong>Payment Term:</strong> <?= htmlspecialchars($loan['paymentTerm']) ?></p>
    <p><strong>Loan Purpose:</strong> <?= htmlspecialchars($loan['loanPurpose']) ?></p>
  <?php else: ?>
    <p>No loan information found.</p>
  <?php endif; ?>
</section>

<section>
  <h3>Employment Information</h3>
  <?php if (isset($employment) && $employment): ?>
    <p><strong>Employer Name:</strong> <?= htmlspecialchars($employment['employerName']) ?></p>
    <p><strong>Employer Address:</strong> <?= htmlspecialchars($employment['employerAdd']) ?></p>
    <p><strong>Rank:</strong> <?= htmlspecialchars($employment['rank']) ?></p>
    <p><strong>Current Position:</strong> <?= htmlspecialchars($employment['curPosition']) ?></p>
    <p><strong>Length of Service:</strong> <?= htmlspecialchars($employment['curLengthService']) ?></p>
  <?php else: ?>
    <p>No employment information found.</p>
  <?php endif; ?>
</section>

<section>
  <h3>Credit Card Details</h3>
  <?php if (!empty($creditCards)): ?>
    <?php foreach ($creditCards as $card): ?>
      <div>
        <p><strong>Card Number:</strong> <?= htmlspecialchars($card['cardNo']) ?></p>
        <p><strong>Type:</strong> <?= htmlspecialchars($card['creditCard']) ?></p>
        <p><strong>Credit Limit:</strong> <?= htmlspecialchars($card['creditLimit']) ?></p>
        <p><strong>Expiry Date:</strong> <?= htmlspecialchars($card['expiryDate']) ?></p>
      </div>
      <hr>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No credit card information found.</p>
  <?php endif; ?>
</section>

</body>
</html>