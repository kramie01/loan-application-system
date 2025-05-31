<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['applicant_id'])) {
    die('Error: No applicant ID found. Please login first.');
}

$applicantID = $_SESSION['applicant_id'];

// Fetch applicant and employment info
$sql = "
    SELECT a.*, e.*
    FROM applicant_info a
    LEFT JOIN employment_info e ON a.tinNumber = e.tinNumber
    WHERE a.applicantID = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $applicantID);
$stmt->execute();
$result = $stmt->get_result();
$applicant = $result->fetch_assoc();

// Fetch credit card info
$creditCards = [];
$cardSql = "SELECT * FROM creditcard_info WHERE applicantID = ?";
$cardStmt = $conn->prepare($cardSql);
$cardStmt->bind_param("i", $applicantID);
$cardStmt->execute();
$cardResult = $cardStmt->get_result();
while ($row = $cardResult->fetch_assoc()) {
    $creditCards[] = $row;
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Client - Profile</title>
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
      <h1>Client Profile</h1>
      <p>Here is your profile</p>

        <!-- Applicant Information -->
        <div class="form-section">
          <h2>Applicant Information</h2>
          <div class="form-group">
            <label for="bankAccountNumber">Bank Account Number</label>
      <input id="bankAccountNumber" name="bankAccountNumber" value="<?= htmlspecialchars($applicant['bankAccountNumber'] ?? '') ?>" readonly />

      <label for="emailAdrs">Email Address</label>
      <input id="emailAdrs" name="emailAdrs" type="email" value="<?= htmlspecialchars($applicant['emailAdrs'] ?? '') ?>" readonly />

      <label for="applicantName">Full Name</label>
      <input id="applicantName" name="applicantName" value="<?= htmlspecialchars($applicant['applicantName'] ?? '') ?>" readonly />

      <label for="motherMaidenName">Mother's Maiden Name</label>
      <input id="motherMaidenName" name="motherMaidenName" value="<?= htmlspecialchars($applicant['motherMaidenName'] ?? '') ?>" readonly />

      <label for="age">Age</label>
      <input id="age" type="number" name="age" value="<?= htmlspecialchars($applicant['age'] ?? '') ?>" readonly />

      <label for="birthDate">Birthdate</label>
      <input id="birthDate" type="date" name="birthDate" value="<?= htmlspecialchars($applicant['birthDate'] ?? '') ?>" readonly />

      <label for="birthPlace">Birthplace</label>
      <input id="birthPlace" name="birthPlace" value="<?= htmlspecialchars($applicant['birthPlace'] ?? '') ?>" readonly />

      <label for="civilStatus">Civil Status</label>
      <select id="civilStatus" name="civilStatus" disabled>
        <option value="">-- Select Civil Status --</option>
        <option value="Single" <?= $applicant['civilStatus'] === 'Single' ? 'selected' : '' ?>>Single</option>
        <option value="Married" <?= $applicant['civilStatus'] === 'Married' ? 'selected' : '' ?>>Married</option>
        <option value="Legally Separated" <?= $applicant['civilStatus'] === 'Legally Separated' ? 'selected' : '' ?>>Legally Separated</option>
        <option value="Widow / Widower" <?= $applicant['civilStatus'] === 'Widow / Widower' ? 'selected' : '' ?>>Widow / Widower</option>
      </select>

      <label for="gender">Gender</label>
      <select id="gender" name="gender" disabled>
        <option value="">-- Select Gender --</option>
        <option value="Male" <?= $applicant['gender'] === 'M' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $applicant['gender'] === 'F' ? 'selected' : '' ?>>Female</option>
      </select>

      <label for="nationality">Nationality</label>
      <input id="nationality" name="nationality" value="<?= htmlspecialchars($applicant['nationality'] ?? '') ?>" readonly />

      <label for="dependentsNum">Number of Dependents</label>
      <input id="dependentsNum" type="number" name="dependentsNum" value="<?= htmlspecialchars($applicant['dependentsNum'] ?? '') ?>" readonly />

      <label for="educAttainment">Educational Attainment</label>
      <select id="educAttainment" name="educAttainment" disabled>
        <option value="">-- Select Educational Attainment --</option>
        <option value="High School" <?= $applicant['educAttainment'] === 'High School' ? 'selected' : '' ?>>High School</option>
        <option value="College" <?= $applicant['educAttainment'] === 'College' ? 'selected' : '' ?>>College</option>
        <option value="Vocational" <?= $applicant['educAttainment'] === 'Vocational' ? 'selected' : '' ?>>Vocational</option>
        <option value="Postgraduate" <?= $applicant['educAttainment'] === 'Postgraduate' ? 'selected' : '' ?>>Postgraduate</option>
      </select>

      <label for="homePNum">Home Phone Number</label>
      <input id="homePNum" name="homePNum" value="<?= htmlspecialchars($applicant['homePNum'] ?? '') ?>" readonly />

      <label for="mobilePNum">Mobile Phone Number</label>
      <input id="mobilePNum" name="mobilePNum" value="<?= htmlspecialchars($applicant['mobilePNum'] ?? '') ?>" readonly />

      <label for="presentHomeAdrs">Present Home Address</label>
      <textarea id="presentHomeAdrs" name="presentHomeAdrs" readonly><?= htmlspecialchars($applicant['presentHomeAdrs'] ?? '') ?></textarea>

      <label for="lengthOfStay">Length of Stay</label>
      <input id="lengthOfStay" name="lengthOfStay" value="<?= htmlspecialchars($applicant['lengthOfStay'] ?? '') ?>" readonly />

      <label for="adrsStatus">Address Status</label>
      <select id="adrsStatus" name="adrsStatus" disabled>
        <option value="">-- Select Address Status --</option>
        <option value="Owned" <?= $applicant['adrsStatus'] === 'Owned' ? 'selected' : '' ?>>Owned</option>
        <option value="Living with Relatives" <?= $applicant['adrsStatus'] === 'Living with Relatives' ? 'selected' : '' ?>>Living with Relatives</option>
        <option value="Renting" <?= $applicant['adrsStatus'] === 'Renting' ? 'selected' : '' ?>>Renting</option>
        <option value="Mortgaged" <?= $applicant['adrsStatus'] === 'Mortgaged' ? 'selected' : '' ?>>Mortgaged</option>
      </select>

      <label for="monthlyPay">Monthly Pay</label>
      <input id="monthlyPay" name="monthlyPay" value="<?= htmlspecialchars($applicant['monthlyPay'] ?? '') ?>" readonly />
          </div>
        </div>

        <!-- Employment Information -->
        <div class="form-section">
          <h2>Employment Information</h2>
          <div class="form-group">
            <label for="tinNumber">TIN Number</label>
            <input id="tinNumber" name="tinNumber" value="<?= htmlspecialchars($applicant['tinNumber'] ?? '') ?>" readonly />

            <label for="employerName">Employer Name</label>
            <input id="employerName" name="employerName" value="<?= htmlspecialchars($applicant['employerName'] ?? '') ?>" readonly />

            <label for="employerAdd">Employer Address</label>
            <input id="employerAdd" name="employerAdd" value="<?= htmlspecialchars($applicant['employerAdd'] ?? '') ?>" readonly />

            <label for="typeOfEmploy">Type of Employment</label>
            <select id="typeOfEmploy" name="typeOfEmploy" disabled>
              <option value="">-- Select --</option>
              <option value="Private" <?= $applicant['typeOfEmploy'] === 'Private' ? 'selected' : '' ?>>Private</option>
              <option value="Government" <?= $applicant['typeOfEmploy'] === 'Government' ? 'selected' : '' ?>>Government</option>
              <option value="Professional" <?= $applicant['typeOfEmploy'] === 'Professional' ? 'selected' : '' ?>>Professional</option>
              <option value="Self-Employed" <?= $applicant['typeOfEmploy'] === 'Self-Employed' ? 'selected' : '' ?>>Self-Employed</option>
              <option value="Unemployed" <?= $applicant['typeOfEmploy'] === 'Unemployed' ? 'selected' : '' ?>>Unemployed</option>
              <option value="Retired" <?= $applicant['typeOfEmploy'] === 'Retired' ? 'selected' : '' ?>>Retired</option>
            </select>

            <label for="employStatus">Employment Status</label>
            <select id="employStatus" name="employStatus" disabled>
              <option value="">-- Select --</option>
              <option value="Permanent" <?= $applicant['employStatus'] === 'Permanent' ? 'selected' : '' ?>>Permanent</option>
              <option value="Probationary" <?= $applicant['employStatus'] === 'Probationary' ? 'selected' : '' ?>>Probationary</option>
              <option value="Contractual" <?= $applicant['employStatus'] === 'Contractual' ? 'selected' : '' ?>>Contractual</option>
              <option value="Professional" <?= $applicant['employStatus'] === 'Professional' ? 'selected' : '' ?>>Professional</option>
              <option value="Consultant" <?= $applicant['employStatus'] === 'Consultant' ? 'selected' : '' ?>>Consultant</option>
              <option value="Special Occupation" <?= $applicant['employStatus'] === 'Special Occupation' ? 'selected' : '' ?>>Special Occupation</option>
            </select>

            <label for="rank">Rank</label>
            <select id="rank" name="rank" disabled>
              <option value="">-- Select --</option>
              <option value="Rank & File" <?= $applicant['rank'] === 'Rank & File' ? 'selected' : '' ?>>Rank & File</option>
              <option value="Junior Officer" <?= $applicant['rank'] === 'Junior Officer' ? 'selected' : '' ?>>Junior Officer</option>
              <option value="Middle Manager" <?= $applicant['rank'] === 'Middle Manager' ? 'selected' : '' ?>>Middle Manager</option>
              <option value="Senior Executive" <?= $applicant['rank'] === 'Senior Executive' ? 'selected' : '' ?>>Senior Executive</option>
              <option value="Self-Employed" <?= $applicant['rank'] === 'Self-Employed' ? 'selected' : '' ?>>Self-Employed</option>
              <option value="Others" <?= $applicant['rank'] === 'Others' ? 'selected' : '' ?>>Others</option>
            </select>

            <div id="otherRankContainer" style="<?= ($applicant['rank'] ?? '') === 'Others' ? 'display: block;' : 'display: none;' ?>; margin-top: 10px;">
              <label for="otherRank">Please specify</label>
              <input type="text" id="otherRank" name="otherRank" value="<?= htmlspecialchars($applicant['otherRank'] ?? '') ?>" readonly />
            </div>

            <label for="curPosition">Current Position</label>
            <input id="curPosition" name="curPosition" value="<?= htmlspecialchars($applicant['curPosition'] ?? '') ?>" readonly />

            <label for="sssNum">SSS Number</label>
            <input id="sssNum" name="sssNum" value="<?= htmlspecialchars($applicant['sssNum'] ?? '') ?>" readonly />

            <label for="dateOfHire">Date of Hire</label>
            <input id="dateOfHire" type="date" name="dateOfHire" value="<?= htmlspecialchars($applicant['dateOfHire'] ?? '') ?>" readonly />

            <label for="curLengthService">Current Length of Service</label>
            <input id="curLengthService" name="curLengthService" value="<?= htmlspecialchars($applicant['curLengthService'] ?? '') ?>" readonly />

            <label for="officeNum">Office Number</label>
            <input id="officeNum" name="officeNum" value="<?= htmlspecialchars($applicant['officeNum'] ?? '') ?>" readonly />

            <label for="officeEmailAdd">Office Email Address</label>
            <input id="officeEmailAdd" type="email" name="officeEmailAdd" value="<?= htmlspecialchars($applicant['officeEmailAdd'] ?? '') ?>" readonly />

            <label for="hrContactPerson">HR Contact Person</label>
            <input id="hrContactPerson" name="hrContactPerson" value="<?= htmlspecialchars($applicant['hrContactPerson'] ?? '') ?>" readonly />

            <label for="officeTelNum">Office Telephone Number</label>
            <input id="officeTelNum" name="officeTelNum" value="<?= htmlspecialchars($applicant['officeTelNum'] ?? '') ?>" readonly />

            <label for="dayToCall">Best Day to Call</label>
            <input id="dayToCall" name="dayToCall" value="<?= htmlspecialchars($applicant['dayToCall'] ?? '') ?>" readonly />

            <label for="prevEmployer">Previous Employer</label>
            <input id="prevEmployer" name="prevEmployer" value="<?= htmlspecialchars($applicant['prevEmployer'] ?? '') ?>" readonly />

            <label for="prevLengthService">Previous Length of Service</label>
            <input id="prevLengthService" name="prevLengthService" value="<?= htmlspecialchars($applicant['prevLengthService'] ?? '') ?>" readonly />

            <label for="prevPosition">Previous Position</label>
            <input id="prevPosition" name="prevPosition" value="<?= htmlspecialchars($applicant['prevPosition'] ?? '') ?>" readonly />

            <label for="totalYrsWorking">Total Years Working</label>
            <input id="totalYrsWorking" name="totalYrsWorking" value="<?= htmlspecialchars($applicant['totalYrsWorking'] ?? '') ?>" readonly />
          </div>
        </div>

        <!-- Credit Card Info -->
      <div class="form-section">
        <h2>Credit Card Information</h2>
        <div id="creditCardsContainer">
          <?php foreach ($creditCards as $index => $card): ?>
            <div class="credit-card-group">
              <label for="cardNo_<?= $index ?>">Card Number</label>
              <input id="cardNo_<?= $index ?>" name="cardNo[]" value="<?= htmlspecialchars($card['cardNo'] ?? '') ?>" required readonly />

              <label for="creditCard_<?= $index ?>">Credit Card Type</label>
              <input id="creditCard_<?= $index ?>" name="creditCard[]" value="<?= htmlspecialchars($card['creditCard'] ?? '') ?>" required readonly />

              <label for="creditLimit_<?= $index ?>">Credit Limit</label>
              <input id="creditLimit_<?= $index ?>" name="creditLimit[]" value="<?= htmlspecialchars($card['creditLimit'] ?? '') ?>" required readonly />

              <label for="expiryDate_<?= $index ?>">Expiry Date</label>
              <input id="expiryDate_<?= $index ?>" type="date" name="expiryDate[]" value="<?= htmlspecialchars($card['expiryDate'] ?? '') ?>" required readonly />
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <button type="submit" class="form-submit-btn">Update Profile</button>
</body>
</html>