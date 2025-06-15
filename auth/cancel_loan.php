<?php
include '../includes/config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
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
        header('Location: ../pages/cloandetails_page.php');
        exit();
    }
    
    // Get email from session
    $emailAdrs = $_SESSION['email'];
    
    // Get applicant ID
    $stmt = $pdo->prepare("SELECT applicantID FROM applicant_info WHERE emailAdrs = ?");
    $stmt->execute([$emailAdrs]);
    $applicant = $stmt->fetch();
    
    if (!$applicant) {
        $_SESSION['error'] = "Applicant not found.";
        header('Location: ../pages/cloandetails_page.php');
        exit();
    }
    
    // Verify that the loan belongs to this applicant and get loan details
    $stmt = $pdo->prepare("SELECT loan_id, loanAmount, status FROM loan_info WHERE loan_id = ? AND applicantID = ?");
    $stmt->execute([$loan_id, $applicant['applicantID']]);
    $loan = $stmt->fetch();
    
    if (!$loan) {
        $_SESSION['error'] = "Loan not found or you don't have permission to cancel it.";
        header('Location: ../pages/cloandetails_page.php');
        exit();
    }
    
    if ($loan['status'] !== 'Pending') {
        $_SESSION['error'] = "Only pending loans can be cancelled.";
        header('Location: ../pages/cloandetails_page.php');
        exit();
    }
    
    // Delete the loan from the database
    $stmt = $pdo->prepare("DELETE FROM loan_info WHERE loan_id = ? AND applicantID = ?");
    $stmt->execute([$loan_id, $applicant['applicantID']]);
    
    // Format loan ID for display
    $formattedLoanId = 'L' . str_pad($loan['loan_id'], 3, '0', STR_PAD_LEFT);
    
    $_SESSION['success'] = "Loan application {$formattedLoanId} has been successfully cancelled and removed from the system.";
    header('Location: ../pages/cloandetails_page.php');
    exit();
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header('Location: ../pages/cloandetails_page.php');
    exit();
}
?>