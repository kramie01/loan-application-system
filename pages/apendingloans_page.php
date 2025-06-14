<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/home_page.php");
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
    
    // Get all pending loan applications - using applicantID as the foreign key
    $stmt = $pdo->prepare("
        SELECT 
            l.loan_id,
            l.loanAmount,
            l.paymentTerm,
            l.loanPurpose,
            l.applicantID,
            a.applicantName,
            a.emailAdrs,
            a.mobilePNum,
            a.monthlyPay
        FROM loan_info l
        INNER JOIN applicant_info a ON l.applicantID = a.applicantID
        WHERE l.status = 'Pending'
    ");
    
    $stmt->execute();
    $pendingLoans = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - View Approve Loans</title>
    <link rel="stylesheet" href="../assets/css/admin_style.css" />
</head>

<body>
    <header>
        <img src="../assets/images/lendease_white.png" alt="Loan Logo" />
        <h1>LendEase - Admin Loan Application System</h1>
    </header>

    <div class="main-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <a href="../pages/adashboard_page.php">Dashboard</a>
            <a href="../pages/apendingloans_page.php" class="active">Pending Loans</a>
            <a href="../pages/aactiveloans_page.php">Active Loans</a>
            <a href="../pages/apaidloans_page.php">Paid Loans</a>
            <a href="../auth/logout.php">Logout</a>
        </div>

        <div class="content">

            <h1>PENDING LOAN APPLICATIONS</h1>
            <p>Review and approve pending loan applications.</p>

            <section class="loan-details-section">
            <h3>Pending Loans</h3>
            <p style="margin-bottom: 20px; color: #666;">View all pending loan applications</p>

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
                <?php elseif (empty($pendingLoans)): ?>
                    <div class="no-loans-message">
                        <p>No pending loan applications at this time.</p>
                    </div>
                <?php else: ?>
                    <div class="loan-table-container">
                        <table class="loan-table">
                            <thead>
                                <tr>
                                    <th>LOAN ID</th>
                                    <th>APPLICANT</th>
                                    <th>LOAN DETAILS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingLoans as $loan): ?>
                                    <tr>
                                        <td class="loan-id">
                                            <?php echo 'L' . str_pad($loan['loan_id'], 3, '0', STR_PAD_LEFT); ?>
                                        </td>
                                        <td class="applicant-info">
                                            <div class="applicant-name"><?php echo htmlspecialchars($loan['applicantName']); ?></div>
                                            <div class="applicant-email"><?php echo htmlspecialchars($loan['emailAdrs']); ?></div>
                                            <div class="applicant-phone"><?php echo htmlspecialchars($loan['mobilePNum']); ?></div>
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
                                        <td class="action">
                                            <div class="action-buttons">
                                                <button class="btn btn-view" onclick="viewProfile(<?php echo $loan['applicantID']; ?>, <?php echo $loan['loan_id']; ?>)">
                                                    VIEW PROFILE
                                                </button>
                                                <button class="btn btn-approve1" onclick="approveLoan(<?php echo $loan['loan_id']; ?>, '<?php echo htmlspecialchars($loan['applicantName']); ?>')">
                                                    APPROVE
                                                </button>
                                            </div>
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

    <!-- Approve Confirmation Modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Approve Loan Application</h4>
                <span class="close" onclick="closeApproveModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="approve-icon">✅</div>
                <p><strong>Are you sure you want to approve <br> this loan application?</strong></p>
                <div id="approveDetails" class="approval-details"></div>
                <p>This will change the loan status <br> from "Pending" to "Active".</p>
            </div>
            <form id="approveLoanForm" method="POST" action="../auth/approve_loan.php">
                <input type="hidden" id="approveLoanId" name="loanId">
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeApproveModal()">CANCEL</button>
                    <button type="submit" class="btn btn-approve">APPROVE</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content profile-modal-content">
            <div class="modal-header">
                <h4>Applicant Profile</h4>
                <span class="close" onclick="closeProfileModal()">&times;</span>
            </div>
            <div class="modal-body profile-modal-body">
                <div id="profileContent">
                    <div class="loading">Loading profile...</div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin_script.js"></script>

</body>
</html>