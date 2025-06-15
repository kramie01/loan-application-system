<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied";
    exit();
}

// Get applicant ID and loan ID from URL
$applicantId = isset($_GET['applicantId']) ? (int)$_GET['applicantId'] : 0;
$loanId = isset($_GET['loanId']) ? (int)$_GET['loanId'] : 0;

if (!$applicantId || !$loanId) {
    http_response_code(400);
    echo "Invalid parameters";
    exit();
}

list($hostName, $port) = explode(':', $host);
$charset = 'utf8mb4';

$dsn = "mysql:host=$hostName;port=$port;dbname=$database;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
    
    // Get applicant profile data using applicantID
    $stmt = $pdo->prepare("SELECT * FROM applicant_info WHERE applicantID = ?");
    $stmt->execute([$applicantId]);
    $profileData = $stmt->fetch();
    
    // Get employment data using tinNumber from applicant_info
    $employmentData = null;
    if ($profileData && $profileData['tinNumber']) {
        $stmt = $pdo->prepare("SELECT * FROM employment_info WHERE tinNumber = ?");
        $stmt->execute([$profileData['tinNumber']]);
        $employmentData = $stmt->fetch();
    }
    
    // Get credit card data using applicantID
    $stmt = $pdo->prepare("SELECT * FROM creditcard_info WHERE applicantID = ?");
    $stmt->execute([$applicantId]);
    $creditCards = $stmt->fetchAll();
    
    if (!$profileData) {
        echo "Profile not found";
        exit();
    }
    
} catch (PDOException $e) {
    echo "Database error: " . htmlspecialchars($e->getMessage());
    exit();
}
?>

<!-- Personal Information -->
<div class="profile-section">
    <h3>Personal Information</h3>
    <div class="profile-grid">

        <div class="profile-item">
            <label>Bank Account Number</label>
            <span><?php echo htmlspecialchars($profileData['bankAccountNumber']); ?></span>
        </div>

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

        <div class="profile-item">
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
            <label>Mother's Maiden Name</label>
            <span><?php echo htmlspecialchars($profileData['motherMaidenName']); ?></span>
        </div>

    </div>
</div>

<!-- Employment Information -->
<?php if ($employmentData): ?>
    <div class="profile-section">
        <h3>Employment Information</h3>

        <div class="profile-grid">

            <div class="profile-item">
                <label>TIN Number</label>
                <span><?php echo htmlspecialchars($profileData['tinNumber']); ?></span>
            </div>

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

            <?php 
            $dateOfHire = $employmentData['dateOfHire'] ?? '';
            if (!empty($dateOfHire) && $dateOfHire !== '0000-00-00' && strtotime($dateOfHire) !== false && strtotime($dateOfHire) > 0): 
            ?>
            <div class="profile-item">
                <label>Date of Hire</label>
                <span><?php echo date('F d, Y', strtotime($dateOfHire)); ?></span>
            </div>
            <?php endif; ?>

            <div class="profile-item">
                <label>Current Length of Service (Year/s)</label>
                <span><?php echo htmlspecialchars($employmentData['curLengthService']); ?> </span>
            </div>

            <div class="profile-item">
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

            <?php if ($employmentData['dayToCall']): ?>
            <div class="profile-item">
                <label>Best Day to Call</label>
                <span><?php echo htmlspecialchars($employmentData['dayToCall']); ?></span>
            </div>
            <?php endif; ?>

            <?php if ($employmentData['prevEmployer']): ?>
            <div class="profile-item">
                <label>Previous Employer</label>
                <span><?php echo htmlspecialchars($employmentData['prevEmployer']); ?></span>
            </div>
            <?php endif; ?>

            <?php if ($employmentData['prevLengthService']): ?>
            <div class="profile-item">
                <label>Previous Length of Service (Year/s)</label>
                <span><?php echo htmlspecialchars($employmentData['prevLengthService']); ?></span>
            </div>
            <?php endif; ?>

            <?php if ($employmentData['prevPosition']): ?>
            <div class="profile-item">
                <label>Previous Position</label>
                <span><?php echo htmlspecialchars($employmentData['prevPosition']); ?></span>
            </div>
            <?php endif; ?>

            <div class="profile-item">
                <label>Total Years Working</label>
                <span><?php echo htmlspecialchars($employmentData['totalYrsWorking']); ?> </span>
            </div>

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
                        <div class="profile-item full-width">
                            <label>Card Number</label>
                            <span><?php echo $card['cardNo']; ?></span>
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