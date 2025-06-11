<?php
include '../includes/config.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/home_page.php');
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
    
    // Get loan ID from POST
    $loan_id = $_POST['loanId'] ?? null;
    
    if (!$loan_id) {
        $_SESSION['error'] = "Loan ID is required.";
        header('Location: ../pages/adashboard_page.php');
        exit();
    }
    
    // Get loan details and verify it exists and is pending
    $stmt = $pdo->prepare("
        SELECT l.loan_id, l.status, l.loanAmount, a.applicantName 
        FROM loan_info l
        INNER JOIN applicant_info a ON l.applicantID = a.applicantID
        WHERE l.loan_id = ?
    ");
    $stmt->execute([$loan_id]);
    $loan = $stmt->fetch();
    
    if (!$loan) {
        $_SESSION['error'] = "Loan not found.";
        header('Location: ../pages/adashboard_page.php');
        exit();
    }
    
    if ($loan['status'] !== 'Pending') {
        $_SESSION['error'] = "Only pending loans can be approved.";
        header('Location: ../pages/adashboard_page.php');
        exit();
    }
    
    // Update loan status to Active
    $stmt = $pdo->prepare("UPDATE loan_info 
                          SET status = 'Active'
                          WHERE loan_id = ?");
    $stmt->execute([$loan_id]);
    
    // Format loan ID for display
    $formattedLoanId = 'L' . str_pad($loan['loan_id'], 3, '0', STR_PAD_LEFT);
    
    $_SESSION['success'] = "Loan {$formattedLoanId} for {$loan['applicantName']} has been approved successfully!";
    header('Location: ../pages/adashboard_page.php');
    exit();
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header('Location: ../pages/adashboard_page.php');
    exit();
}
?>