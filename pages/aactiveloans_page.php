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
    
    // Get all active loan applications
    $stmt = $pdo->prepare("
        SELECT 
            l.loan_id,
            l.loanAmount,
            l.paymentTerm,
            l.loanPurpose,
            a.applicantName,
            a.emailAdrs,
            a.mobilePNum,
            a.monthlyPay
        FROM loan_info l
        INNER JOIN applicant_info a ON l.applicantID = a.applicantID
        WHERE l.status = 'Active'
    ");
    
    $stmt->execute();
    $activeLoans = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <title>Admin - View Active Loans</title>
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
                    <a href="../pages/apendingloans_page.php">Pending Loans</a>
                    <a href="../pages/aactiveloans_page.php" class="active">Active Loans</a>
                    <a href="../pages/apaidloans_page.php">Paid Loans</a>
                    <a href="../auth/logout.php">Logout</a>
                </div>

                <div class="content">
                    
                    <h1>ACTIVE LOAN APPLICATIONS</h1>
                    <p>View active loan applications.</p>

                    <section class="loan-details-section">
                        <h3>Active Loans</h3>
                        <p style="margin-bottom: 20px; color: #666;">View all currently active loan accounts</p>

                        <?php if (isset($error)): ?>
                            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                        <?php elseif (empty($activeLoans)): ?>
                            <div class="no-loans-message">
                                <p>No active loans at this time.</p>
                            </div>
                        <?php else: ?>
                            <div class="loan-table-container">
                                <table class="loan-table">
                                    <thead>
                                        <tr>
                                            <th>LOAN ID</th>
                                            <th>APPLICANT</th>
                                            <th>LOAN DETAILS</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($activeLoans as $loan): ?>
                                            <tr>
                                                <td class="loan-id">
                                                    <?php echo 'L' . str_pad($loan['loan_id'], 3, '0', STR_PAD_LEFT); ?>
                                                </td>
                                                <td class="loan-details">
                                                    <div class="detail-item">
                                                        <strong>Name:</strong> <?php echo htmlspecialchars($loan['applicantName']); ?>
                                                    </div>
                                                    <div class="detail-item">
                                                        <strong>Email:</strong> <?php echo htmlspecialchars($loan['emailAdrs']); ?>
                                                    </div>
                                                    <div class="detail-item">
                                                        <strong>Phone:</strong> <?php echo htmlspecialchars($loan['mobilePNum']); ?>
                                                    </div>
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
                                                    <span class="status-badge status-active">
                                                        Active
                                                    </span>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>
            </div>
        </body>
    </html>