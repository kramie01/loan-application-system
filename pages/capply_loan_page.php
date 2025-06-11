<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../pages/home_page.php');
    exit();
}

include '../includes/config.php';

// Check for existing loans and profile
list($hostName, $port) = explode(':', $host);
$charset = 'utf8mb4';

$dsn = "mysql:host=$hostName;port=$port;dbname=$database;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$hasExistingLoan = false;
$noProfile = false;
$existingLoanDetails = null;

try {
    $pdo = new PDO($dsn, $user, $password, $options);
    
    $emailAdrs = $_SESSION['email'];
    
    // Check if email already exists in applicant_info
    $stmt = $pdo->prepare("SELECT applicantID FROM applicant_info WHERE emailAdrs = ?");
    $stmt->execute([$emailAdrs]);
    $applicant = $stmt->fetch();
    
    if ($applicant) {
        $applicantID = $applicant['applicantID'];
        
        // Check for active loans (Pending or Active)
        $stmt = $pdo->prepare("SELECT loan_id, loanAmount, paymentTerm, loanPurpose, status 
                              FROM loan_info 
                              WHERE applicantID = ? AND status IN ('Pending', 'Active')");
        $stmt->execute([$applicantID]);
        $activeLoan = $stmt->fetch();
        
        if ($activeLoan) {
            $hasExistingLoan = true;
            $existingLoanDetails = $activeLoan;
        }
    } else {
        $noProfile = true;
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Client - Apply Loan Application</title>
    <link rel="stylesheet" href="../assets/css/capply_style.css" />
    <link rel="stylesheet" href="../assets/css/loan_modal.css" />
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
          <a href="../pages/capply_loan_page.php" class="active">Apply for a Loan</a>
          <a href="../pages/cloandetails_page.php">Loan Details</a>
          <a href="../pages/cprofile_page.php">Profile</a>
          <a href="../auth/logout.php">Logout</a>
        </div>

        <div class="content">
            <h1>Apply for a Loan</h1>
            <p>Complete the form below to apply for a loan.</p>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="../auth/submit_loan.php" method="POST" class="loan-form" id="loanForm">
                <!-- Loan Information -->
                <div class="form-section">
                    <h2>Loan Details</h2>
                    <div class="form-group">
                        <label for="loanAmount">Loan Amount (₱)</label>
                        <input type="number" id="loanAmount" name="loanAmount" required />

                        <label for="paymentTerm">Payment Term (months)</label>
                        <select id="paymentTerm" name="paymentTerm" required >
                            <option value="">-- Select Term --</option>
                            <option value="6">6 months</option>
                            <option value="12">12 months</option>
                            <option value="18">18 months</option>
                            <option value="24">24 months</option>
                            <option value="36">36 months</option>
                        </select>

                        <label for="loanPurpose">Loan Purpose</label>
                        <select id="loanPurpose" name="loanPurpose" required >
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
                            <input type="text" id="otherLoanPurpose" name="otherLoanPurpose" maxlength="100" />
                        </div>
                    </div>
                </div>

                <button type="submit" class="form-submit-btn">Submit Application</button>
            </form>
        </div>
    </div>

    <!-- Existing Loan Modal -->
    <div id="existingLoanModal" class="modal" <?php echo $hasExistingLoan ? 'style="display: block;"' : ''; ?>>
        <div class="modal-content">
            <div class="modal-header">
                <h4>Existing Loan Found</h4>
            </div>
            <div class="modal-body">
                <div class="warning-icon">⚠️</div>
                <p>You already have an active loan application. <br> You can only have one loan at a time.</p>
                
                <?php if ($hasExistingLoan && $existingLoanDetails): ?>
                <div class="existing-loan-details">
                    <h5>Current Loan Details:</h5>
                    <div class="loan-info">
                        <p><strong>Loan ID:</strong> L<?php echo str_pad($existingLoanDetails['loan_id'], 3, '0', STR_PAD_LEFT); ?></p>
                        <p><strong>Amount:</strong> ₱<?php echo number_format($existingLoanDetails['loanAmount'], 2); ?></p>
                        <p><strong>Term:</strong> <?php echo $existingLoanDetails['paymentTerm']; ?> months</p>
                        <p><strong>Purpose:</strong> <?php echo htmlspecialchars($existingLoanDetails['loanPurpose']); ?></p>
                        <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($existingLoanDetails['status']); ?>"><?php echo $existingLoanDetails['status']; ?></span></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <p>Please settle your current loan before applying for a new one.</p>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-primary" onclick="goToLoanDetails()">View Loan Details</button>
            </div>
        </div>
    </div>

    <!-- No Profile Modal -->
    <div id="noProfileModal" class="modal" <?php echo $noProfile ? 'style="display: block;"' : ''; ?>>
        <div class="modal-content">
            <div class="modal-header">
                <h4>Profile Required</h4>
            </div>
            <div class="modal-body">
                <div class="info-icon">ℹ️</div>
                <p>You need to complete your profile before applying for a loan.</p>
                <p>Please fill out your personal information, employment details, and other required information to proceed with your loan application.</p>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="goToDashboard()">Go to Dashboard</button>
                <button type="button" class="btn btn-primary" onclick="goToProfile()">Complete Profile</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-header success-header">
                <h4>Application Submitted Successfully!</h4>
            </div>
            <div class="modal-body">
                <div class="success-icon">✅</div>
                <p>Your loan application has been submitted successfully!</p>
                <div class="success-details">
                    <p>Your application is now being reviewed. You will be notified once the status changes.</p>
                    <p><strong>What's next?</strong></p>
                    <ul>
                        <li>Your application will be reviewed by our team</li>
                        <li>You can track your application status in "View Loan Details"</li>
                        <li>You'll receive updates on your application progress</li>
                    </ul>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="goToDashboard()">Go to Dashboard</button>
                <button type="button" class="btn btn-primary" onclick="goToLoanDetails()">View Loan Details</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/capply_loan_script.js"></script>
</body>
</html>
