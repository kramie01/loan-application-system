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

// Initialize default values
$stats = [];
$totalLoans = 0;

try {
    $pdo = new PDO($dsn, $user, $password, $options);
    
    // Get loan statistics
    $statsStmt = $pdo->prepare("
        SELECT 
            status,
            COUNT(*) as count
        FROM loan_info 
        GROUP BY status
    ");
    $statsStmt->execute();
    $statsResult = $statsStmt->fetchAll();
    
    // Convert to associative array for easier access
    foreach ($statsResult as $row) {
        $stats[$row['status']] = $row['count'];
    }
    
    // Calculate total loans
    $totalLoans = array_sum($stats);
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Helper function to get stat value safely
function getStatValue($stats, $key) {
    return isset($stats[$key]) ? $stats[$key] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin_style.css" />
</head>

<body>
    <header>
        <img src="../assets/images/lendease_white.png" alt="Loan Logo" />
        <h1>LendEase - Admin Loan Application System</h1>
        </div>
    </header>

    <div class="main-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <a href="../pages/adashboard_page.php" class="active">Dashboard</a>
            <a href="../pages/apendingloans_page.php">Pending Loans</a>
            <a href="../pages/aactiveloans_page.php">Active Loans</a>
            <a href="../pages/apaidloans_page.php">Paid Loans</a>
            <a href="../auth/logout.php">Logout</a>
        </div>

        <div class="content">

        <h1>Welcome, <span><?= htmlspecialchars($_SESSION['username']) ?>!</span></h1>
        <p>Here is the admin page, you can manage loan application</p>

            <section class="loan-details-section">
                <h3>Admin Dashboard</h3>
                <p class="page-description">Overview of loan management system</p>

                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card pending">
                        <div class="stat-header">
                            <h4>Pending Loans</h4>
                            <span class="stat-icon">⏳</span>
                        </div>
                        <div class="stat-number"><?php echo getStatValue($stats, 'Pending'); ?></div>
                        <div class="stat-description">Applications awaiting approval</div>
                    </div>

                    <div class="stat-card active">
                        <div class="stat-header">
                            <h4>Active Loans</h4>
                            <span class="stat-icon">✅</span>
                        </div>
                        <div class="stat-number"><?php echo getStatValue($stats, 'Active'); ?></div>
                        <div class="stat-description">Currently active loans</div>
                    </div>

                    <div class="stat-card paid">
                        <div class="stat-header">
                            <h4>Paid Loans</h4>
                            <span class="stat-icon">💰</span>
                        </div>
                        <div class="stat-number"><?php echo getStatValue($stats, 'Paid'); ?></div>
                        <div class="stat-description">Completed loan payments</div>
                    </div>

                    <div class="stat-card total">
                        <div class="stat-header">
                            <h4>Total Loans</h4>
                            <span class="stat-icon">📊</span>
                        </div>
                        <div class="stat-number"><?php echo $totalLoans; ?></div>
                        <div class="stat-description">All loan applications</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
