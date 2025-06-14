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
    <!-- Modal -->
    <div id="modal" class="modal-overlay">
        <div class="modal-content">
            <p><?php echo $_SESSION['complete_profile_msg']; ?></p>
            <button onclick="document.getElementById('modal').style.display='none'">OK</button>
        </div>
    </div>

    <?php unset($_SESSION['complete_profile_msg']); ?>
<?php endif; ?>

<?php if (!empty($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

  <header>
    <img src="../assets/images/lendease_white.png" alt="Loan Logo" />
    <h1>LendEase - Client Loan Application System</h1>
  </header>

    <div class="content">
      <h1>COMPLETE YOUR PROFILE</h1>
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
            <input id="motherMaidenName" name="motherMaidenName" required />

            <label for="age">Age</label>
            <input id="age" type="number" name="age" required />

            <label for="birthDate">Birthdate</label>
            <input id="birthDate" type="date" name="birthDate" required />

            <label for="birthPlace">Birthplace</label>
            <input id="birthPlace" name="birthPlace" required />

            <label for="civilStatus">Civil Status</label>
            <select id="civilStatus" name="civilStatus" required >
              <option value="">-- SELECT CIVIL STATUS --</option>
              <option value="Single">SINGLE</option>
              <option value="Married">MARRIED</option>
              <option value="Legally Separated">LEGALLY SEPERATED</option>
              <option value="Widow / Widower">WIDOW / WIDOWER</option>
            </select>

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required >
              <option value="">-- SELECT GENDER --</option>
              <option value="Male">MALE</option>
              <option value="Female">FEMALE</option>
            </select>

            <label for="nationality">Nationality</label>
            <input id="nationality" name="nationality" required />

            <label for="dependentsNum">Number of Dependents</label>
            <input id="dependentsNum" type="number" name="dependentsNum" required />

            <label for="educAttainment">Educational Attainment</label>
            <select id="educAttainment" name="educAttainment" required >
              <option value="">-- SELECT EDUCATIONAL ATTAINMENT --</option>
              <option value="High School">HIGH SCHOOL</option>
              <option value="College">COLLEGE</option>
              <option value="Vocational">VOCATIONAL</option>
              <option value="Postgraduate">POSTGRADUATE</option>
            </select>

            <label for="homePNum">Home Phone Number</label>
            <input id="homePNum" name="homePNum" />

            <label for="mobilePNum">Mobile Phone Number</label>
            <input id="mobilePNum" name="mobilePNum" required />

            <label for="presentHomeAdrs">Present Home Address</label>
            <textarea id="presentHomeAdrs" name="presentHomeAdrs" required ></textarea>

            <label for="lengthOfStay">Length of Stay</label>
            <input id="lengthOfStay" name="lengthOfStay" required />

            <label for="adrsStatus">Address Status</label>
            <select id="adrsStatus" name="adrsStatus" required >
              <option value="">-- SELECT ADDRESS STATUS --</option>
              <option value="Owned">OWNED</option>
              <option value="Living with Relatives">LIVING WITH RELATIVES</option>
              <option value="Renting">RENTING</option>
              <option value="Mortgaged">MORTGAGED</option>
            </select>

            <label for="monthlyPay">Monthly Pay</label>
            <input id="monthlyPay" name="monthlyPay" required />
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
            <select id="typeOfEmploy" name="typeOfEmploy" required >
              <option value="">-- SELECT --</option>
              <option value="Private">PRIVATE</option>
              <option value="Government">GOVERNMENT</option>
              <option value="Professional">PROFESSIONAL</option>
              <option value="Self-Employed">SELF-EMPLOYED</option>
              <option value="Unemployed">UNEMPLOYED</option>
              <option value="Retired">RETIRED</option>
            </select>

            <label for="employStatus">Employment Status</label>
            <select id="employStatus" name="employStatus">
              <option value="">-- SELECT --</option>
              <option value="Permanent">PERMANENT</option>
              <option value="Probationary">PROBATIONARY</option>
              <option value="Contractual">CONTRACTUAL</option>
              <option value="Professional">PROFESSIONAL</option>
              <option value="Consultant">CONSULTANT</option>
              <option value="Special Occupation">SPECIAL OCCUPATION</option>
              <option value="Unemployed">UNEMPLOYED</option>
            </select>

            <label for="rank">Rank</label>
            <select id="rank" name="rank" required >
              <option value="">-- SELECT --</option>
              <option value="Rank & File">RANK & FILE</option>
              <option value="Junior Officer">JUNIOR OFFICER</option>
              <option value="Middle Manager">MIDDLE MANAGER</option>
              <option value="Senior Executive">SENIOR EXECUTIVE</option>
              <option value="Self-Employed">SELF-EMPLOYED</option>
              <option value="Others">OTHERS</option>
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
            <input id="totalYrsWorking" name="totalYrsWorking" required />
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