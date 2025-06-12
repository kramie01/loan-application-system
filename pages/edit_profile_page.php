<?php
session_start();
//Validate if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../pages/home_page.php');
    exit();
}

//Configures and establishes database connection
include '../includes/config.php';
list($hostName, $port) = explode(':', $host);
$charset = 'utf8mb4';

$dsn = "mysql:host=$hostName;port=$port;dbname=$database;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
    
    // Get email from session
    $emailAdrs = $_SESSION['email'];
    
    // Get applicant ID
    $stmt = $pdo->prepare("SELECT applicantID FROM applicant_info WHERE emailAdrs = ?");
    $stmt->execute([$emailAdrs]);
    $applicant = $stmt->fetch();
    
    $profile = [];
    if ($applicant) {
        // Fetch all profile data of the user
        $stmt = $pdo->prepare("SELECT * FROM applicant_info WHERE applicantID = ?");
        $stmt->execute([$applicant['applicantID']]);
        $profile = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Double checks if an account is logged in
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
  <title>Client - Edit Profile</title>
  <link rel="stylesheet" href="../assets/css/capply_style.css" />
 
</head>
<body>

<header>
    <img src="../assets/images/lendease_white.png" alt="Loan Logo" />
    <h1>LendEase - Client Loan Application System</h1>
  </header>

<div class="main-container">
    <!-- Sidebar Navigation -->
    <div class="sidebar">
      <a href="../pages/cdashboard_page.php">Dashboard</a>
      <a href="../pages/capply_loan_page.php">Apply for a Loan</a>
      <a href="../pages/cloandetails_page.php">Loan Details</a>
      <a href="../pages/cprofile_page.php" class="active">Profile</a>
      <a href="../auth/logout.php">Logout</a>
    </div>

  <div class="content">
      <h1>UPDATE MY PROFILE</h1>
      <p>You can edit your profile and save it.</p>

      <form id="updateLoanForm" method="POST" action="../auth/update_profile.php"> 
        <!-- Applicant Information -->
        <div class="form-section">
          <h2>Applicant Information</h2>
        <div class="form-group">


        <label for="bankAccountNumber">Bank Account Number</label>
        <input id="bankAccountNumber" name="bankAccountNumber" value="<?= htmlspecialchars($applicant['bankAccountNumber'] ?? '') ?>" required readonly />

        <label for="emailAdrs">Email Address</label>
        <input id="emailAdrs" name="emailAdrs" type="email" value="<?= htmlspecialchars($applicant['emailAdrs'] ?? '') ?>" required readonly />

        <label for="applicantName">Full Name</label>
        <input id="applicantName" name="applicantName" value="<?= htmlspecialchars($applicant['applicantName'] ?? '') ?>" required readonly />

        <label for="motherMaidenName">Mother's Maiden Name</label>
        <input id="motherMaidenName" name="motherMaidenName" value="<?= htmlspecialchars($applicant['motherMaidenName'] ?? '') ?>" required readonly />

        <label for="age">Age</label>
        <input id="age" type="number" name="age" value="<?= htmlspecialchars($applicant['age'] ?? '') ?>" required readonly />

        <label for="birthDate">Birthdate</label>
        <input id="birthDate" type="date" name="birthDate" value="<?= htmlspecialchars($applicant['birthDate'] ?? '') ?>" required readonly />

        <label for="birthPlace">Birthplace</label>
        <input id="birthPlace" name="birthPlace" value="<?= htmlspecialchars($applicant['birthPlace'] ?? '') ?>" required readonly />

        <label for="civilStatus">Civil Status</label>
        <select id="civilStatus" name="civilStatus" required disabled >
          <option value="">-- SELECT CIVIL STATUS --</option>
          <option value="Single" <?= $applicant['civilStatus'] === 'Single' ? 'selected' : '' ?>>SINGLE</option>
          <option value="Married" <?= $applicant['civilStatus'] === 'Married' ? 'selected' : '' ?>>MARRIED</option>
          <option value="Legally Separated" <?= $applicant['civilStatus'] === 'Legally Separated' ? 'selected' : '' ?>>LEGALLY SEPARATED</option>
          <option value="Widow / Widower" <?= $applicant['civilStatus'] === 'Widow / Widower' ? 'selected' : '' ?>>WIDOW / WIDOWER</option>
        </select>

        <label for="gender">Gender</label>
        <select id="gender" name="gender" required disabled >
          <option value="">-- SELECT GENDER --</option>
          <option value="Male" <?= $applicant['gender'] === 'M' ? 'selected' : '' ?>>MALE</option>
          <option value="Female" <?= $applicant['gender'] === 'F' ? 'selected' : '' ?>>FEMALE</option>
        </select>

        <label for="nationality">Nationality</label>
        <input id="nationality" name="nationality" value="<?= htmlspecialchars($applicant['nationality'] ?? '') ?>" required readonly />

        <label for="dependentsNum">Number of Dependents</label>
        <input id="dependentsNum" type="number" name="dependentsNum" value="<?= htmlspecialchars($applicant['dependentsNum'] ?? '') ?>" required />

        <label for="educAttainment">Educational Attainment</label>
        <select id="educAttainment" name="educAttainment" required disabled >
          <option value="">-- SELECT EDUCATIONAL ATTAINMENT --</option>
          <option value="High School" <?= $applicant['educAttainment'] === 'High School' ? 'selected' : '' ?>>HIGH SCHOOL</option>
          <option value="College" <?= $applicant['educAttainment'] === 'College' ? 'selected' : '' ?>>COLLEGE</option>
          <option value="Vocational" <?= $applicant['educAttainment'] === 'Vocational' ? 'selected' : '' ?>>VOCATIONAL</option>
          <option value="Postgraduate" <?= $applicant['educAttainment'] === 'Postgraduate' ? 'selected' : '' ?>>POSTGRADUATE</option>
        </select>

        <label for="homePNum">Home Phone Number</label>
        <input id="homePNum" name="homePNum" value="<?= htmlspecialchars($applicant['homePNum'] ?? '') ?>" readonly />

        <label for="mobilePNum">Mobile Phone Number</label>
        <input id="mobilePNum" name="mobilePNum" value="<?= htmlspecialchars($applicant['mobilePNum'] ?? '') ?>" required readonly />

        <label for="presentHomeAdrs">Present Home Address</label>
        <textarea id="presentHomeAdrs" name="presentHomeAdrs" required ><?= htmlspecialchars($applicant['presentHomeAdrs'] ?? '') ?></textarea>

        <label for="lengthOfStay">Length of Stay</label>
        <input id="lengthOfStay" name="lengthOfStay" value="<?= htmlspecialchars($applicant['lengthOfStay'] ?? '') ?>" required />

        <label for="adrsStatus">Address Status</label>
        <select id="adrsStatus" name="adrsStatus" required >
          <option value="">-- SELECT ADDRESS STATUS --</option>
          <option value="Owned" <?= $applicant['adrsStatus'] === 'Owned' ? 'selected' : '' ?>>OWNED</option>
          <option value="Living with Relatives" <?= $applicant['adrsStatus'] === 'Living with Relatives' ? 'selected' : '' ?>>LIVING WITH RELATIVES</option>
          <option value="Renting" <?= $applicant['adrsStatus'] === 'Renting' ? 'selected' : '' ?>>RENTING</option>
          <option value="Mortgaged" <?= $applicant['adrsStatus'] === 'Mortgaged' ? 'selected' : '' ?>>MORTGAGED</option>
        </select>

        <label for="monthlyPay">Monthly Pay</label>
        <input id="monthlyPay" name="monthlyPay" value="<?= htmlspecialchars($applicant['monthlyPay'] ?? '') ?>" required
        <?= ($applicant['adrsStatus'] !== 'Mortgaged' && $applicant['adrsStatus'] !== 'Renting') ? 'disabled' : '' ?> />

        <script>
          const adrsStatus = document.getElementById('adrsStatus');
          const monthlyPay = document.getElementById('monthlyPay');

          function toggleMonthlyPay() {
              if (adrsStatus.value === 'Mortgaged') {
                  monthlyPay.disabled = false;  // enable input
              }
              else if (adrsStatus.value === 'Renting') {
                  monthlyPay.disabled = false;  // enable input
              } else {
                  monthlyPay.disabled = true;   // disable input
                  monthlyPay.value = '';        // optionally clear value
              }
          }

          // Run on page load in case the value is pre-selected
          toggleMonthlyPay();

          // Add event listener to toggle on change
          adrsStatus.addEventListener('change', toggleMonthlyPay);
        </script>
      </div>
  </div>
        
        <!-- Employment Information -->
        <div class="form-section">
          <h2>Employment Information</h2>
          <div class="form-group">

            <label for="tinNumber">TIN Number</label>
            <input id="tinNumber" name="tinNumber" value="<?= htmlspecialchars($applicant['tinNumber'] ?? '') ?>" required readonly />

            <label for="employerName">Employer Name</label>
            <input id="employerName" name="employerName" value="<?= htmlspecialchars($applicant['employerName'] ?? '') ?>" />

            <label for="employerAdd">Employer Address</label>
            <input id="employerAdd" name="employerAdd" value="<?= htmlspecialchars($applicant['employerAdd'] ?? '') ?>" />

            <label for="typeOfEmploy">Type of Employment</label>
            <select id="typeOfEmploy" name="typeOfEmploy" required >
              <option value="">-- SELECT --</option>
              <option value="Private" <?= $applicant['typeOfEmploy'] === 'Private' ? 'selected' : '' ?>>PRIVATE</option>
              <option value="Government" <?= $applicant['typeOfEmploy'] === 'Government' ? 'selected' : '' ?>>GOVERNMENT</option>
              <option value="Professional" <?= $applicant['typeOfEmploy'] === 'Professional' ? 'selected' : '' ?>>PROFESSIONAL</option>
              <option value="Self-Employed" <?= $applicant['typeOfEmploy'] === 'Self-Employed' ? 'selected' : '' ?>>SELF-EMPLOYED</option>
              <option value="Unemployed" <?= $applicant['typeOfEmploy'] === 'Unemployed' ? 'selected' : '' ?>>UNEMPLOYED</option>
              <option value="Retired" <?= $applicant['typeOfEmploy'] === 'Retired' ? 'selected' : '' ?>>RETIRED</option>
            </select>

            <label for="employStatus">Employment Status</label>
            <select id="employStatus" name="employStatus" >
              <option value="">-- SELECT --</option>
              <option value="Permanent" <?= $applicant['employStatus'] === 'Permanent' ? 'selected' : '' ?>>PERMANENT</option>
              <option value="Probationary" <?= $applicant['employStatus'] === 'Probationary' ? 'selected' : '' ?>>PROBATIONARY</option>
              <option value="Contractual" <?= $applicant['employStatus'] === 'Contractual' ? 'selected' : '' ?>>CONTRACTUAL</option>
              <option value="Professional" <?= $applicant['employStatus'] === 'Professional' ? 'selected' : '' ?>>PROFESSIONAL</option>
              <option value="Consultant" <?= $applicant['employStatus'] === 'Consultant' ? 'selected' : '' ?>>CONSULTANT</option>
              <option value="Special Occupation" <?= $applicant['employStatus'] === 'Special Occupation' ? 'selected' : '' ?>>SPECIAL OCCUPATION</option>
            </select>

            <label for="rank">Rank</label>
            <select id="rank" name="rank" required >
              <option value="">-- SELECT --</option>
              <option value="Rank & File" <?= $applicant['rank'] === 'Rank & File' ? 'selected' : '' ?>>RANK & FILE</option>
              <option value="Junior Officer" <?= $applicant['rank'] === 'Junior Officer' ? 'selected' : '' ?>>JUNIOR OFFICER</option>
              <option value="Middle Manager" <?= $applicant['rank'] === 'Middle Manager' ? 'selected' : '' ?>>MIDDLE MANAGER</option>
              <option value="Senior Executive" <?= $applicant['rank'] === 'Senior Executive' ? 'selected' : '' ?>>SENIOR EXECUTIVE</option>
              <option value="Self-Employed" <?= $applicant['rank'] === 'Self-Employed' ? 'selected' : '' ?>>SELF-EMPLOYED</option>
              <option value="Others" <?= $applicant['rank'] === 'Others' ? 'selected' : '' ?>>OTHERS</option>
            </select>

            <div id="otherRankContainer" style="<?= ($applicant['rank'] ?? '') === 'Others' ? 'display: block;' : 'display: none;' ?>; margin-top: 10px;">
              <label for="otherRank">Please specify</label>
              <input type="text" id="otherRank" name="otherRank" value="<?= htmlspecialchars($applicant['otherRank'] ?? '') ?>" readonly />
            </div>

            <label for="curPosition">Current Position</label>
            <input id="curPosition" name="curPosition" value="<?= htmlspecialchars($applicant['curPosition'] ?? '') ?>" />

            <label for="sssNum">SSS Number</label>
            <input id="sssNum" name="sssNum" value="<?= htmlspecialchars($applicant['sssNum'] ?? '') ?>" readonly />

            <label for="dateOfHire">Date of Hire</label>
            <input id="dateOfHire" type="date" name="dateOfHire" value="<?= htmlspecialchars($applicant['dateOfHire'] ?? '') ?>"  />

            <label for="curLengthService">Current Length of Service</label>
            <input id="curLengthService" name="curLengthService" value="<?= htmlspecialchars($applicant['curLengthService'] ?? '') ?>"  />

            <label for="officeNum">Office Number</label>
            <input id="officeNum" name="officeNum" value="<?= htmlspecialchars($applicant['officeNum'] ?? '') ?>"  />

            <label for="officeEmailAdd">Office Email Address</label>
            <input id="officeEmailAdd" type="email" name="officeEmailAdd" value="<?= htmlspecialchars($applicant['officeEmailAdd'] ?? '') ?>"  />

            <label for="hrContactPerson">HR Contact Person</label>
            <input id="hrContactPerson" name="hrContactPerson" value="<?= htmlspecialchars($applicant['hrContactPerson'] ?? '') ?>"  />

            <label for="officeTelNum">Office Telephone Number</label>
            <input id="officeTelNum" name="officeTelNum" value="<?= htmlspecialchars($applicant['officeTelNum'] ?? '') ?>"  />

            <label for="dayToCall">Best Day to Call</label>
            <input id="dayToCall" name="dayToCall" value="<?= htmlspecialchars($applicant['dayToCall'] ?? '') ?>"  />

            <label for="prevEmployer">Previous Employer</label>
            <input id="prevEmployer" name="prevEmployer" value="<?= htmlspecialchars($applicant['prevEmployer'] ?? '') ?>"  />

            <label for="prevLengthService">Previous Length of Service</label>
            <input id="prevLengthService" name="prevLengthService" value="<?= htmlspecialchars($applicant['prevLengthService'] ?? '') ?>"  />

            <label for="prevPosition">Previous Position</label>
            <input id="prevPosition" name="prevPosition" value="<?= htmlspecialchars($applicant['prevPosition'] ?? '') ?>"  />

            <label for="totalYrsWorking">Total Years Working</label>
            <input id="totalYrsWorking" name="totalYrsWorking" value="<?= htmlspecialchars($applicant['totalYrsWorking'] ?? '') ?>" required />
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

        <div style="display: flex; justify-content: space-between; gap: 12px;">
          <button type="submit" class="form-submit-btn" onclick="window.location.href='cprofile_page.php'">Save Changes</button>
          <button type="button" class="form-cancel-btn" onclick="window.location.href='cprofile_page.php'">Cancel</button>
        </div>
              
          </div>
      </form>

<script src="../assets/js/ccomplete_profile_script.js"></script>

</body>
</html>
