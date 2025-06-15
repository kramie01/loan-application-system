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

            <label for="birthDate">Birthdate</label>
            <input id="birthDate" type="date" name="birthDate" required />

            <label for="age">Age</label>
            <input id="age" type="number" name="age" required readonly />

            <script>
              const ageInput = document.getElementById('age');
              const birthDateInput = document.getElementById('birthDate');

              function calculateAge(birthDate) {
                  const today = new Date();
                  const birth = new Date(birthDate);
                  let age = today.getFullYear() - birth.getFullYear();
                  const monthDiff = today.getMonth() - birth.getMonth();
                  
                  // If birthday hasn't occurred this year yet, subtract 1
                  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                      age--;
                  }
                  
                  return age;
              }

              function updateAge() {
                  const birthDate = birthDateInput.value;
                  
                  if (birthDate) {
                      const age = calculateAge(birthDate);
                      ageInput.value = age;
                      
                      console.log('Calculated age:', age); // Debug line
                      
                      // Check if age is below 18 - show alert only
                      if (age < 18) {
                          alert('Enter your correct birthdate and ensure you are at least 18 years old.');
                          birthDateInput.value = ''; // Clear the invalid date
                          ageInput.value = ''; // Clear the age
                      }
                  } else {
                      // Clear age if no birthdate is selected
                      ageInput.value = '';
                  }
              }

              // Add event listener to birthdate input
              birthDateInput.addEventListener('change', updateAge);

              // Run on page load if birthdate is already set
              if (birthDateInput.value) {
                  updateAge();
              }
            </script>

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
            <select id="adrsStatus" name="adrsStatus" required>
              <option value="">-- SELECT ADDRESS STATUS --</option>
              <option value="Owned" <?= (isset($_SESSION['adrsStatus']) && $_SESSION['adrsStatus'] === 'Owned') ? 'selected' : '' ?>>OWNED</option>
              <option value="Living with Relatives" <?= (isset($_SESSION['adrsStatus']) && $_SESSION['adrsStatus'] === 'Living with Relatives') ? 'selected' : '' ?>>LIVING WITH RELATIVES</option>
              <option value="Renting" <?= (isset($_SESSION['adrsStatus']) && $_SESSION['adrsStatus'] === 'Renting') ? 'selected' : '' ?>>RENTING</option>
              <option value="Mortgaged" <?= (isset($_SESSION['adrsStatus']) && $_SESSION['adrsStatus'] === 'Mortgaged') ? 'selected' : '' ?>>MORTGAGED</option>
            </select>

            <label for="monthlyPay">Monthly Pay</label>
            <input type="number" id="monthlyPay" name="monthlyPay" 
                  value="<?= htmlspecialchars($_SESSION['monthlyPay'] ?? '') ?>" 
                  <?= (isset($_SESSION['adrsStatus']) && ($_SESSION['adrsStatus'] === 'Mortgaged' || $_SESSION['adrsStatus'] === 'Renting')) ? '' : 'disabled' ?> />

            <script>
              const adrsStatus = document.getElementById('adrsStatus');
              const monthlyPay = document.getElementById('monthlyPay');

              function toggleMonthlyPay() {
                  console.log('Current selection:', adrsStatus.value); // Debug line
                  
                  if (adrsStatus.value === 'Mortgaged' || adrsStatus.value === 'Renting') {
                      monthlyPay.disabled = false;
                      monthlyPay.required = true;
                  } else {
                      monthlyPay.disabled = true;
                      monthlyPay.required = false;
                      monthlyPay.value = '';
                  }
              }

              // Run immediately (no need for DOMContentLoaded since script is after elements)
              toggleMonthlyPay();

              // Add event listener
              adrsStatus.addEventListener('change', toggleMonthlyPay);
            </script>
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
          <select id="typeOfEmploy" name="typeOfEmploy" required>
            <option value="">-- SELECT --</option>
            <option value="Private" <?= (isset($_SESSION['typeOfEmploy']) && $_SESSION['typeOfEmploy'] === 'Private') ? 'selected' : '' ?>>PRIVATE</option>
            <option value="Government" <?= (isset($_SESSION['typeOfEmploy']) && $_SESSION['typeOfEmploy'] === 'Government') ? 'selected' : '' ?>>GOVERNMENT</option>
            <option value="Professional" <?= (isset($_SESSION['typeOfEmploy']) && $_SESSION['typeOfEmploy'] === 'Professional') ? 'selected' : '' ?>>PROFESSIONAL</option>
            <option value="Self-Employed" <?= (isset($_SESSION['typeOfEmploy']) && $_SESSION['typeOfEmploy'] === 'Self-Employed') ? 'selected' : '' ?>>SELF-EMPLOYED</option>
            <option value="Unemployed" <?= (isset($_SESSION['typeOfEmploy']) && $_SESSION['typeOfEmploy'] === 'Unemployed') ? 'selected' : '' ?>>UNEMPLOYED</option>
            <option value="Retired" <?= (isset($_SESSION['typeOfEmploy']) && $_SESSION['typeOfEmploy'] === 'Retired') ? 'selected' : '' ?>>RETIRED</option>
          </select>

          <label for="employStatus">Employment Status</label>
          <select id="employStatus" name="employStatus" 
                  <?= (isset($_SESSION['typeOfEmploy']) && ($_SESSION['typeOfEmploy'] === 'Unemployed' || $_SESSION['typeOfEmploy'] === 'Retired')) ? 'disabled' : 'required' ?>>
            <option value="">-- SELECT --</option>
            <option value="Permanent" <?= (isset($_SESSION['employStatus']) && $_SESSION['employStatus'] === 'Permanent') ? 'selected' : '' ?>>PERMANENT</option>
            <option value="Probationary" <?= (isset($_SESSION['employStatus']) && $_SESSION['employStatus'] === 'Probationary') ? 'selected' : '' ?>>PROBATIONARY</option>
            <option value="Contractual" <?= (isset($_SESSION['employStatus']) && $_SESSION['employStatus'] === 'Contractual') ? 'selected' : '' ?>>CONTRACTUAL</option>
            <option value="Professional" <?= (isset($_SESSION['employStatus']) && $_SESSION['employStatus'] === 'Professional') ? 'selected' : '' ?>>PROFESSIONAL</option>
            <option value="Consultant" <?= (isset($_SESSION['employStatus']) && $_SESSION['employStatus'] === 'Consultant') ? 'selected' : '' ?>>CONSULTANT</option>
            <option value="Special Occupation" <?= (isset($_SESSION['employStatus']) && $_SESSION['employStatus'] === 'Special Occupation') ? 'selected' : '' ?>>SPECIAL OCCUPATION</option>
          </select>

          <label for="rank">Rank</label>
          <select id="rank" name="rank" 
                  <?= (isset($_SESSION['typeOfEmploy']) && ($_SESSION['typeOfEmploy'] === 'Unemployed' || $_SESSION['typeOfEmploy'] === 'Retired')) ? 'disabled' : 'required' ?>>
            <option value="">-- SELECT --</option>
            <option value="Rank & File" <?= (isset($_SESSION['rank']) && $_SESSION['rank'] === 'Rank & File') ? 'selected' : '' ?>>RANK & FILE</option>
            <option value="Junior Officer" <?= (isset($_SESSION['rank']) && $_SESSION['rank'] === 'Junior Officer') ? 'selected' : '' ?>>JUNIOR OFFICER</option>
            <option value="Middle Manager" <?= (isset($_SESSION['rank']) && $_SESSION['rank'] === 'Middle Manager') ? 'selected' : '' ?>>MIDDLE MANAGER</option>
            <option value="Senior Executive" <?= (isset($_SESSION['rank']) && $_SESSION['rank'] === 'Senior Executive') ? 'selected' : '' ?>>SENIOR EXECUTIVE</option>
            <option value="Self-Employed" <?= (isset($_SESSION['rank']) && $_SESSION['rank'] === 'Self-Employed') ? 'selected' : '' ?>>SELF-EMPLOYED</option>
            <option value="Others" <?= (isset($_SESSION['rank']) && $_SESSION['rank'] === 'Others') ? 'selected' : '' ?>>OTHERS</option>
          </select>

          <div id="otherRankContainer" style="<?= (isset($_SESSION['rank']) && $_SESSION['rank'] === 'Others') ? 'display: block;' : 'display: none;' ?> margin-top: 10px;">
            <label for="otherRank">Please specify</label>
            <input type="text" id="otherRank" name="otherRank" 
                  value="<?= htmlspecialchars($_SESSION['otherRank'] ?? '') ?>"
                  <?= (isset($_SESSION['typeOfEmploy']) && ($_SESSION['typeOfEmploy'] === 'Unemployed' || $_SESSION['typeOfEmploy'] === 'Retired')) ? 'disabled' : '' ?> />
          </div>

          <label for="curPosition">Current Position</label>
          <input type="text" id="curPosition" name="curPosition" 
                value="<?= htmlspecialchars($_SESSION['curPosition'] ?? '') ?>"
                <?= (isset($_SESSION['typeOfEmploy']) && ($_SESSION['typeOfEmploy'] === 'Unemployed' || $_SESSION['typeOfEmploy'] === 'Retired')) ? 'disabled' : '' ?> />

          <script>
            const typeOfEmploy = document.getElementById('typeOfEmploy');
            const employStatus = document.getElementById('employStatus');
            const rank = document.getElementById('rank');
            const otherRankContainer = document.getElementById('otherRankContainer');
            const otherRank = document.getElementById('otherRank');
            const curPosition = document.getElementById('curPosition');

            function toggleEmploymentFields() {
                console.log('Employment type:', typeOfEmploy.value); // Debug line
                
                const shouldDisable = typeOfEmploy.value === 'Unemployed' || typeOfEmploy.value === 'Retired';
                
                if (shouldDisable) {
                    // Disable fields but keep them visible
                    employStatus.disabled = true;
                    rank.disabled = true;
                    otherRank.disabled = true;
                    curPosition.disabled = true;
                    // Remove required attributes
                    employStatus.required = false;
                    rank.required = false;
                    otherRank.required = false;
                    // Clear values
                    employStatus.value = '';
                    rank.value = '';
                    otherRank.value = '';
                    curPosition.value = '';
                    otherRankContainer.style.display = 'none';
                } else {
                    // Enable fields
                    employStatus.disabled = false;
                    rank.disabled = false;
                    otherRank.disabled = false;
                    curPosition.disabled = false;
                    // Add required attributes back
                    employStatus.required = true;
                    rank.required = true;
                }
            }

            function toggleOtherRank() {
                console.log('Rank selected:', rank.value); // Debug line
                
                // Only show "Others" field if rank is not disabled and "Others" is selected
                if (!rank.disabled && rank.value === 'Others') {
                    otherRankContainer.style.display = 'block';
                    otherRank.required = true;
                } else {
                    otherRankContainer.style.display = 'none';
                    otherRank.required = false;
                    otherRank.value = '';
                }
            }

            // Run on page load
            toggleEmploymentFields();
            toggleOtherRank();

            // Add event listeners
            typeOfEmploy.addEventListener('change', toggleEmploymentFields);
            rank.addEventListener('change', toggleOtherRank);
          </script>

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