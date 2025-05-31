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
  <title>Client - Loan Application</title>
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
      <a href="../pages/capply_loan_page.php">Apply for a Loan</a>
      <a href="../pages/cdetails_page.php">View Loan Details</a>
      <a href="../pages/cprofile_page.php">Profile</a>
      <a href="../auth/logout.php">Logout</a>
    </div>

    <div class="content">
      <h1>Apply for a Loan</h1>
      <p>Complete the form below to apply for a loan.</p>

      <form action="../auth/submit_loan.php" method="POST" class="loan-form">

        <!-- Loan Information -->
        <div class="form-section">
          <h2>Loan Details</h2>
          <div class="form-group">
            <label for="loanAmount">Loan Amount</label>
            <input id="loanAmount" name="loanAmount" required />

            <label for="paymentTerm">Payment Term (months)</label>
            <select id="paymentTerm" name="paymentTerm" required>
              <option value="">-- Select Term --</option>
              <option value="6">6 months</option>
              <option value="12">12 months</option>
              <option value="18">18 months</option>
              <option value="24">24 months</option>
              <option value="36">36 months</option>
            </select>

            <label for="loanPurpose">Loan Purpose</label>
            <select id="loanPurpose" name="loanPurpose" required>
              <option value="">-- Select Purpose --</option>
              <option value="Travel">Travel</option>
              <option value="Appliance/s">Appliance/s</option>
              <option value="Furniture/Fixtures">Furniture/Fixtures</option>
              <option value="Electronic Gadgets">Electronic Gadgets</option>
              <option value="Personal Consumption">Personal Consumption</option>
              <option value="Hospitalization">Hospitalization</option>
              <option value="Health & Wellness">Health & Wellness</option>
              <option value="Education">Education</option>
              <option value="Balance Transfer">Balance Transfer</option>
              <option value="Special Events">Special Events</option>
              <option value="Home Improvement">Home Improvement</option>
              <option value="Car Repair">Car Repair</option>
              <option value="Others">Others</option>
            </select>

            <div id="otherPurposeContainer" style="display:none;">
              <label for="otherLoanPurpose">Please specify</label>
              <input type="text" id="otherLoanPurpose" name="otherLoanPurpose" required />
            </div>  
          </div>
        </div>

        <button type="submit" class="form-submit-btn">Submit Application</button>
      </form>
    </div>
  </div>

<script src="../assets/js/capply_loan_script.js"></script>

</body>
</html>