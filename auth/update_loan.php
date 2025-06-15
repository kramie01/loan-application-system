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
    
    // Get form data
    $loan_id = $_POST['loanId'] ?? null;
    $loanAmount = $_POST['loanAmount'] ?? null;
    $paymentTerm = $_POST['paymentTerm'] ?? null;
    $loanPurpose = $_POST['loanPurpose'] ?? null;
    $otherLoanPurpose = $_POST['otherLoanPurpose'] ?? null;
    
    // Validate required fields
    if (!$loan_id || !$loanAmount || !$paymentTerm || !$loanPurpose) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: ../pages/cloandetails_page.php');
        exit();
    }
    
    // Handle "Others" loan purpose
    if ($loanPurpose === 'Others' && !empty($otherLoanPurpose)) {
        $loanPurpose = $otherLoanPurpose;
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
    
    // Verify that the loan belongs to this applicant and is still pending
    $stmt = $pdo->prepare("SELECT status FROM loan_info WHERE loan_id = ? AND applicantID = ?");
    $stmt->execute([$loan_id, $applicant['applicantID']]);
    $loan = $stmt->fetch();
    
    if (!$loan) {
        $_SESSION['error'] = "Loan not found or you don't have permission to update it.";
        header('Location: ../pages/cloandetails_page.php');
        exit();
    }
    
    if ($loan['status'] !== 'Pending') {
        $_SESSION['error'] = "Only pending loans can be updated.";
        header('Location: ../pages/cloandetails_page.php');
        exit();
    }
    
    // Update the loan
    $stmt = $pdo->prepare("UPDATE loan_info 
                          SET loanAmount = ?, paymentTerm = ?, loanPurpose = ? 
                          WHERE loan_id = ? AND applicantID = ?");
    $stmt->execute([$loanAmount, $paymentTerm, $loanPurpose, $loan_id, $applicant['applicantID']]);
    
    $_SESSION['success'] = "Loan details updated successfully.";
    header('Location: ../pages/cloandetails_page.php');
    exit();
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header('Location: ../pages/cloandetails_page.php');
    exit();
}
?>