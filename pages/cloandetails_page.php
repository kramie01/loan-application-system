<?php
session_start();
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

try {
    $pdo = new PDO($dsn, $user, $password, $options);
    
    // Get email from session
    $emailAdrs = $_SESSION['email'];
    
    // Get applicant ID
    $stmt = $pdo->prepare("SELECT applicantID FROM applicant_info WHERE emailAdrs = ?");
    $stmt->execute([$emailAdrs]);
    $applicant = $stmt->fetch();
    
    $loans = [];
    if ($applicant) {
        // Get all loans for this applicant
        $stmt = $pdo->prepare("SELECT loan_id, loanAmount, paymentTerm, loanPurpose, status 
                              FROM loan_info 
                              WHERE applicantID = ?");
        $stmt->execute([$applicant['applicantID']]);
        $loans = $stmt->fetchAll();
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
    <title>Client - Loan Details</title>
    <link rel="stylesheet" href="../assets/css/capply_style.css" />
    <link rel="stylesheet" href="../assets/css/cloandetails_style.css" />
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
            <a href="../pages/cloandetails_page.php" class="active">View Loan Details</a>
            <a href="../pages/cprofile_page.php">Profile</a>
            <a href="../auth/logout.php">Logout</a>
        </div>

        <div class="content">
            <section class="loan-details-section">
                <h3>Loan Information</h3>
                
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif (empty($loans)): ?>
                    <div class="no-loans-message">
                        <p>You haven't applied for any loans yet.</p>
                        <a href="../pages/capply_page.php" class="apply-btn">Apply for a Loan</a>
                    </div>
                <?php else: ?>
                    <div class="loan-table-container">
                        <table class="loan-table">
                            <thead>
                                <tr>
                                    <th>LOAN ID</th>
                                    <th>LOAN DETAILS</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($loans as $loan): ?>
                                    <tr>
                                        <td class="loan-id">
                                            <?php echo 'L' . str_pad($loan['loan_id'], 3, '0', STR_PAD_LEFT); ?>
                                        </td>
                                        <td class="loan-details">
                                            <div class="detail-item">
                                                <strong>Amount:</strong> ₱<?php echo number_format($loan['loanAmount'], 2); ?>
                                            </div>
                                            <div class="detail-item">
                                                <strong>Term:</strong> <?php echo htmlspecialchars($loan['paymentTerm']); ?> months
                                            </div>
                                            <div class="detail-item">
                                                <strong>Purpose:</strong> <?php echo htmlspecialchars($loan['loanPurpose']); ?>
                                            </div>
                                        </td>
                                        <td class="status">
                                            <span class="status-badge status-<?php echo strtolower($loan['status']); ?>">
                                                <?php echo htmlspecialchars($loan['status']); ?>
                                            </span>
                                        </td>
                                        <td class="action">
                                            <?php if ($loan['status'] === 'Pending'): ?>
                                                <button class="btn btn-update" onclick="openUpdateModal(<?php echo $loan['loan_id']; ?>, '<?php echo htmlspecialchars($loan['loanAmount']); ?>', '<?php echo htmlspecialchars($loan['paymentTerm']); ?>', '<?php echo htmlspecialchars($loan['loanPurpose']); ?>')">
                                                    Update
                                                </button>
                                                <button class="btn btn-cancel" onclick="cancelLoan(<?php echo $loan['loan_id']; ?>)">
                                                    Cancel
                                                </button>
                                            <?php else: ?>
                                                <span class="no-action">No actions available</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <!-- Update Loan Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Update Loan Details</h4>
                <span class="close" onclick="closeUpdateModal()">&times;</span>
            </div>
            <form id="updateLoanForm" method="POST" action="../auth/update_loan.php">
                <input type="hidden" id="updateLoanId" name="loanId">
                
                <div class="form-group">
                    <label for="updateLoanAmount">Loan Amount (₱):</label>
                    <input type="number" id="updateLoanAmount" name="loanAmount" min="1000" max="500000" step="100" required>
                </div>
                
                <div class="form-group">
                    <label for="updatePaymentTerm">Payment Term (months):</label>
                    <select id="updatePaymentTerm" name="paymentTerm" required>
                        <option value="6">6 months</option>
                        <option value="12">12 months</option>
                        <option value="18">18 months</option>
                        <option value="24">24 months</option>
                        <option value="36">36 months</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="updateLoanPurpose">Loan Purpose:</label>
                    <select id="updateLoanPurpose" name="loanPurpose" required>
                        <option value="Personal">Personal</option>
                        <option value="Business">Business</option>
                        <option value="Education">Education</option>
                        <option value="Medical">Medical</option>
                        <option value="Home Improvement">Home Improvement</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                
                <div class="form-group" id="otherPurposeGroup" style="display: none;">
                    <label for="updateOtherPurpose">Specify Other Purpose:</label>
                    <input type="text" id="updateOtherPurpose" name="otherLoanPurpose" maxlength="100">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeUpdateModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Loan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/cloandetails_script.js"></script>
</body>
</html>
