<?php

session_start();
//Validate if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../pages/home_page.php');
    exit();
}

include '../includes/config.php';
list($hostName, $port) = explode(':', $host);
$charset = 'utf8mb4';

$dsn = "mysql:host=$hostName;port=$port;dbname=$database;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$profileData = null;
$employmentData = null;
$creditCards = [];
$hasProfile = false;

try {
    $pdo = new PDO($dsn, $user, $password, $options);
    
    $emailAdrs = $_SESSION['email'];
    
    // Get applicant profile
    $stmt = $pdo->prepare("SELECT * FROM applicant_info WHERE emailAdrs = ?");
    $stmt->execute([$emailAdrs]);
    $profileData = $stmt->fetch();
    
    if ($profileData) {
        $hasProfile = true;
        
        // Get employment info
        $stmt = $pdo->prepare("SELECT * FROM employment_info WHERE tinNumber = ?");
        $stmt->execute([$profileData['tinNumber']]);
        $employmentData = $stmt->fetch();
        
        // Get credit cards
        $stmt = $pdo->prepare("SELECT * FROM creditcard_info WHERE applicantID = ?");
        $stmt->execute([$profileData['applicantID']]);
        $creditCards = $stmt->fetchAll();
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
            <title>Client - Profile</title>
            <link rel="stylesheet" href="../assets/css/capply_style.css" />
            <link rel="stylesheet" href="../assets/css/profile_view.css" />
            <link rel="stylesheet" href="../assets/css/delete_account_modal.css" />
        </head>

        <body>
            <header>
                <img src="../assets/images/lendease_white.png" alt="Loan Logo" />
                <h1>LendEase - Loan Application System</h1>
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
                <h1>MY PROFILE</h1>
                <p>Here you can view your profile, edit it and even delete it.</p>
                
                    <div class="profile-header">
                        <h1></h1>
                        <div class="profile-actions">
                            <?php if ($hasProfile): ?>
                                <a href="../pages/edit_profile_page.php" class="btn btn-secondary">EDIT PROFILE</a>
                                <button class="btn btn-danger" onclick="openDeleteAccountModal()">DELETE ACCOUNT</button>
                            <?php else: ?>
                                <a href="../pages/ccomplete_profile_page.php" class="btn btn-primary">Complete Profile</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="success-message"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    <?php elseif (!$hasProfile): ?>
                        <div class="no-profile-message">
                            <div class="no-profile-icon">👤</div>
                            <h3>Profile Not Completed</h3>
                            <p>You haven't completed your profile yet. Complete your profile to apply for loans and access all features.</p>
                            <a href="../pages/ccomplete_profile_page.php" class="btn btn-primary">Complete Profile Now</a>
                        </div>
                    <?php else: ?>

                        <!-- Personal Information -->
                        <div class="profile-section">
                            <h3>Personal Information</h3>
                            <div class="profile-grid">
                                <div class="profile-item">
                                    <label>Full Name</label>
                                    <span><?php echo htmlspecialchars($profileData['applicantName']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Email Address</label>
                                    <span><?php echo htmlspecialchars($profileData['emailAdrs']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Age</label>
                                    <span><?php echo htmlspecialchars($profileData['age']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Birth Date</label>
                                    <span><?php echo date('F d, Y', strtotime($profileData['birthDate'])); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Birth Place</label>
                                    <span><?php echo htmlspecialchars($profileData['birthPlace']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Civil Status</label>
                                    <span><?php echo htmlspecialchars($profileData['civilStatus']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Gender</label>
                                    <span><?php echo htmlspecialchars($profileData['gender']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Nationality</label>
                                    <span><?php echo htmlspecialchars($profileData['nationality']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Number of Dependents</label>
                                    <span><?php echo htmlspecialchars($profileData['dependentsNum']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Educational Attainment</label>
                                    <span><?php echo htmlspecialchars($profileData['educAttainment']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Mobile Phone</label>
                                    <span><?php echo htmlspecialchars($profileData['mobilePNum']); ?></span>
                                </div>
                                <?php if ($profileData['homePNum']): ?>
                                <div class="profile-item">
                                    <label>Home Phone</label>
                                    <span><?php echo htmlspecialchars($profileData['homePNum']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="profile-item full-width">
                                    <label>Present Address</label>
                                    <span><?php echo htmlspecialchars($profileData['presentHomeAdrs']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Length of Stay</label>
                                    <span><?php echo htmlspecialchars($profileData['lengthOfStay']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Address Status</label>
                                    <span><?php echo htmlspecialchars($profileData['adrsStatus']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Monthly Pay</label>
                                    <span>₱<?php echo number_format($profileData['monthlyPay'], 2); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Bank Account Number</label>
                                    <span><?php echo htmlspecialchars($profileData['bankAccountNumber']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Mother's Maiden Name</label>
                                    <span><?php echo htmlspecialchars($profileData['motherMaidenName']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>TIN Number</label>
                                    <span><?php echo htmlspecialchars($profileData['tinNumber']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Employment Information -->
                        <?php if ($employmentData): ?>
                        <div class="profile-section">
                            <h3>Employment Information</h3>
                            <div class="profile-grid">
                                <div class="profile-item">
                                    <label>Employer Name</label>
                                    <span><?php echo htmlspecialchars($employmentData['employerName']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Current Position</label>
                                    <span><?php echo htmlspecialchars($employmentData['curPosition']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Employment Type</label>
                                    <span><?php echo htmlspecialchars($employmentData['typeOfEmploy']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Employment Status</label>
                                    <span><?php echo htmlspecialchars($employmentData['employStatus']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Rank</label>
                                    <span><?php echo htmlspecialchars($employmentData['rank']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>SSS Number</label>
                                    <span><?php echo htmlspecialchars($employmentData['sssNum']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Date of Hire</label>
                                    <span><?php echo date('F d, Y', strtotime($employmentData['dateOfHire'])); ?></span>
                                </div>
                                <div class="profile-item">
                                    <label>Current Length of Service</label>
                                    <span><?php echo htmlspecialchars($employmentData['curLengthService']); ?> year/s</span>
                                </div>
                                <div class="profile-item">
                                    <label>Total Years Working</label>
                                    <span><?php echo htmlspecialchars($employmentData['totalYrsWorking']); ?> year/s</span>
                                </div>
                                <div class="profile-item full-width">
                                    <label>Employer Address</label>
                                    <span><?php echo htmlspecialchars($employmentData['employerAdd']); ?></span>
                                </div>
                                <?php if ($employmentData['officeNum']): ?>
                                <div class="profile-item">
                                    <label>Office Number</label>
                                    <span><?php echo htmlspecialchars($employmentData['officeNum']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($employmentData['officeEmailAdd']): ?>
                                <div class="profile-item">
                                    <label>Office Email</label>
                                    <span><?php echo htmlspecialchars($employmentData['officeEmailAdd']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($employmentData['hrContactPerson']): ?>
                                <div class="profile-item">
                                    <label>HR Contact Person</label>
                                    <span><?php echo htmlspecialchars($employmentData['hrContactPerson']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($employmentData['officeTelNum']): ?>
                                <div class="profile-item">
                                    <label>Office Telephone</label>
                                    <span><?php echo htmlspecialchars($employmentData['officeTelNum']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Credit Card Information -->
                        <?php if (!empty($creditCards)): ?>
                        <div class="profile-section">
                            <h3>Credit Card Information</h3>
                            <div class="credit-cards-container">
                                <?php foreach ($creditCards as $index => $card): ?>
                                <div class="credit-card-item">
                                    <h4>Credit Card <?php echo $index + 1; ?></h4>
                                    <div class="profile-grid">
                                        <div class="profile-item">
                                            <label>Card Number</label>
                                            <span>****-****-****-<?php echo substr($card['cardNo'], -4); ?></span>
                                        </div>
                                        <div class="profile-item">
                                            <label>Card Type</label>
                                            <span><?php echo htmlspecialchars($card['creditCard']); ?></span>
                                        </div>
                                        <div class="profile-item">
                                            <label>Credit Limit</label>
                                            <span>₱<?php echo number_format($card['creditLimit'], 2); ?></span>
                                        </div>
                                        <div class="profile-item">
                                            <label>Expiry Date</label>
                                            <span><?php echo date('m/Y', strtotime($card['expiryDate'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <div id="deleteAccountModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header delete-header">
                        <h4>⚠️ Delete Account</h4>
                        <span class="close" onclick="closeDeleteAccountModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="warning-section">
                            <div class="warning-icon">🚨</div>
                            <h3>Are you absolutely sure?</h3>
                            <p><strong>This action cannot be undone!</strong></p>
                        </div>
                        
                        <div class="deletion-details">
                            <p>Deleting your account will permanently remove:</p>
                            <ul>
                                <li>X Your personal profile information</li>
                                <li>X Employment details and history</li>
                                <li>X Credit card information</li>
                                <li>X All loan applications and history</li>
                                <li>X Your user account and login credentials</li>
                            </ul>
                        </div>

                        <div class="confirmation-section">
                            <p><strong>To confirm deletion, please type "DELETE" in the box below:</strong></p>
                            <input type="text" id="deleteConfirmation" placeholder="Type DELETE to confirm" maxlength="6">
                            <div id="confirmationError" class="confirmation-error" style="display: none;">
                                Please type "DELETE" exactly as shown to confirm.
                            </div>
                        </div>

                        <div class="final-warning">
                            <p><strong>⚠️ Warning:</strong> Once deleted, you will need to create a <br> new account and complete your profile again <br> if you want to use our services in the future.</p>
                        </div>
                    </div>
                    <form id="deleteAccountForm" method="POST" action="../auth/delete_account.php">
                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary" onclick="closeDeleteAccountModal()">Cancel</button>
                            <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>Delete My Account</button>
                        </div>
                    </form>
                </div>
            </div>

            <script src="../assets/js/profile_view.js"></script>
            <script src="../assets/js/delete_account.js"></script>
            
        </body>
    </html>