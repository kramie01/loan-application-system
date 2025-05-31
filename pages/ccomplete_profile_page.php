<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'client') {
    header("Location: ../pages/home_page.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Client - Complete Profile</title>
  <link rel="stylesheet" href="../assets/css/capply_style.css" />
</head>

<body>

<?php if (isset($_SESSION['complete_profile_msg'])): ?>
    <p style="color:blue;"><?php 
        echo $_SESSION['complete_profile_msg']; 
        unset($_SESSION['complete_profile_msg']); 
    ?></p>
<?php endif; ?>

<?php if (!empty($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

  <header>
    <img src="../assets/images/lendease_white.png" alt="Loan Logo" />
    <h1>Cashalo - Loan Application System</h1>
  </header>

    <div class="content">
      <h1>Complete Your Profile</h1>
      <p>Complete the form below for your profile.</p>

      <form action="../auth/submit_profile.php" method="POST">

        <!-- Applicant Information -->
        <div class="form-section">
          <h2>Applicant Information</h2>
          <div class="form-group">
            <label for="bankAccountNumber">Bank Account Number</label>
            <input id="bankAccountNumber" name="bankAccountNumber" required />

            <label for="emailAdrs">Email Address</label>
            <input id="emailAdrs" name="emailAdrs" type="email" value="<?= htmlspecialchars($_SESSION['email']) ?>" required readonly />

            <label for="applicantName">Full Name</label>
            <input id="applicantName" name="applicantName" value="<?= htmlspecialchars($_SESSION['fullname']) ?>" required readonly />

            <label for="motherMaidenName">Mother's Maiden Name</label>
            <input id="motherMaidenName" name="motherMaidenName" />

            <label for="age">Age</label>
            <input id="age" type="number" name="age" />

            <label for="birthDate">Birthdate</label>
            <input id="birthDate" type="date" name="birthDate" />

            <label for="birthPlace">Birthplace</label>
            <input id="birthPlace" name="birthPlace" />

            <label for="civilStatus">Civil Status</label>
            <select id="civilStatus" name="civilStatus">
              <option value="">-- Select Civil Status --</option>
              <option value="Single">Single</option>
              <option value="Married">Married</option>
              <option value="Legally Separated">Legally Separated</option>
              <option value="Widow / Widower">Widow / Widower</option>
            </select>

            <label for="gender">Gender</label>
            <select id="gender" name="gender">
              <option value="">-- Select Gender --</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>

            <label for="nationality">Nationality</label>
            <input id="nationality" name="nationality" />

            <label for="dependentsNum">Number of Dependents</label>
            <input id="dependentsNum" type="number" name="dependentsNum" />

            <label for="educAttainment">Educational Attainment</label>
            <select id="educAttainment" name="educAttainment">
              <option value="">-- Select Educational Attainment --</option>
              <option value="High School">High School</option>
              <option value="College">College</option>
              <option value="Vocational">Vocational</option>
              <option value="Postgraduate">Postgraduate</option>
            </select>

            <label for="homePNum">Home Phone Number</label>
            <input id="homePNum" name="homePNum" />

            <label for="mobilePNum">Mobile Phone Number</label>
            <input id="mobilePNum" name="mobilePNum" />

            <label for="presentHomeAdrs">Present Home Address</label>
            <textarea id="presentHomeAdrs" name="presentHomeAdrs"></textarea>

            <label for="lengthOfStay">Length of Stay</label>
            <input id="lengthOfStay" name="lengthOfStay" />

            <label for="adrsStatus">Address Status</label>
            <select id="adrsStatus" name="adrsStatus">
              <option value="">-- Select Address Status --</option>
              <option value="Owned">Owned</option>
              <option value="Living with Relatives">Living with Relatives</option>
              <option value="Renting">Renting</option>
              <option value="Mortgaged">Mortgaged</option>
            </select>

            <label for="monthlyPay">Monthly Pay</label>
            <input id="monthlyPay" name="monthlyPay" />
          </div>
        </div>

        <!-- Employment Information -->
        <div class="form-section">
          <h2>Employment Information</h2>
          <div class="form-group">
            <label for="tinNumber">TIN Number</label>
            <input id="tinNumber" name="tinNumber" required />

            <label for="employerName">Employer Name</label>
            <input id="employerName" name="employerName" />

            <label for="employerAdd">Employer Address</label>
            <input id="employerAdd" name="employerAdd" />

            <label for="typeOfEmploy">Type of Employment</label>
            <select id="typeOfEmploy" name="typeOfEmploy">
              <option value="">-- Select --</option>
              <option value="Private">Private</option>
              <option value="Government">Government</option>
              <option value="Professional">Professional</option>
              <option value="Self-Employed">Self-Employed</option>
              <option value="Unemployed">Unemployed</option>
              <option value="Retired">Retired</option>
            </select>

            <label for="employStatus">Employment Status</label>
            <select id="employStatus" name="employStatus">
              <option value="">-- Select --</option>
              <option value="Permanent">Permanent</option>
              <option value="Probationary">Probationary</option>
              <option value="Contractual">Contractual</option>
              <option value="Professional">Professional</option>
              <option value="Consultant">Consultant</option>
              <option value="Special Occupation">Special Occupation</option>
            </select>

            <label for="rank">Rank</label>
            <select id="rank" name="rank">
              <option value="">-- Select --</option>
              <option value="Rank & File">Rank & File</option>
              <option value="Junior Officer">Junior Officer</option>
              <option value="Middle Manager">Middle Manager</option>
              <option value="Senior Executive">Senior Executive</option>
              <option value="Self-Employed">Self-Employed</option>
              <option value="Others">Others</option>
            </select>

            <div id="otherRankContainer" style="display: none; margin-top: 10px;">
              <label for="otherRank">Please specify</label>
              <input type="text" id="otherRank" name="otherRank" />
            </div>

            <label for="curPosition">Current Position</label>
            <input id="curPosition" name="curPosition" />

            <label for="sssNum">SSS Number</label>
            <input id="sssNum" name="sssNum" />

            <label for="dateOfHire">Date of Hire</label>
            <input id="dateOfHire" type="date" name="dateOfHire" />

            <label for="curLengthService">Current Length of Service</label>
            <input id="curLengthService" name="curLengthService" />

            <label for="officeNum">Office Number</label>
            <input id="officeNum" name="officeNum" />

            <label for="officeEmailAdd">Office Email Address</label>
            <input id="officeEmailAdd" type="email" name="officeEmailAdd" />

            <label for="hrContactPerson">HR Contact Person</label>
            <input id="hrContactPerson" name="hrContactPerson" />

            <label for="officeTelNum">Office Telephone Number</label>
            <input id="officeTelNum" name="officeTelNum" />

            <label for="dayToCall">Best Day to Call</label>
            <input id="dayToCall" name="dayToCall" />

            <label for="prevEmployer">Previous Employer</label>
            <input id="prevEmployer" name="prevEmployer" />

            <label for="prevLengthService">Previous Length of Service</label>
            <input id="prevLengthService" name="prevLengthService" />

            <label for="prevPosition">Previous Position</label>
            <input id="prevPosition" name="prevPosition" />

            <label for="totalYrsWorking">Total Years Working</label>
            <input id="totalYrsWorking" name="totalYrsWorking" />
          </div>
        </div>

        <!-- Credit Card Info -->
      <div class="form-section">
        <h2>Credit Card Information</h2>
        <div id="creditCardsContainer">
          <div class="credit-card-group">
            <label for="cardNo_0">Card Number</label>
            <input id="cardNo_0" name="cardNo[]" required />

            <label for="creditCard_0">Credit Card Type</label>
            <input id="creditCard_0" name="creditCard[]" required />

            <label for="creditLimit_0">Credit Limit</label>
            <input id="creditLimit_0" name="creditLimit[]" required />

            <label for="expiryDate_0">Expiry Date</label>
            <input id="expiryDate_0" type="date" name="expiryDate[]" required />
          </div>
        </div>

        <button type="button" id="addCreditCardBtn">+ Add Credit Card</button>
      </div>

        <button type="submit" class="form-submit-btn">Submit Profile</button>
      </form>
    </div>
  </div>

<script src="../assets/js/ccomplete_profile_script.js"></script>

</body>
</html>