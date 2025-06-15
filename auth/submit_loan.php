<?php
include '../includes/config.php';
session_start(); // Always start session before accessing $_SESSION

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
    $emailAdrs = $_SESSION['email'] ?? null;

    if (!$emailAdrs) {
        $_SESSION['error'] = "Email address not provided in session. Please log in again.";
        header("Location: ../pages/capply_loan_page.php");
        exit();
    }

    // Check if email already exists in applicant_info
    $stmt = $pdo->prepare("SELECT applicantID FROM applicant_info WHERE emailAdrs = ?");
    $stmt->execute([$emailAdrs]);
    $applicant = $stmt->fetch();

    if ($applicant) {
        $applicantID = $applicant['applicantID'];

        // Check for active loans (updated to match new status system)
        $stmt = $pdo->prepare("SELECT status FROM loan_info WHERE applicantID = ? AND status IN ('Pending', 'Active')");
        $stmt->execute([$applicantID]);
        $activeLoan = $stmt->fetch();

        if ($activeLoan) {
            $_SESSION['error'] = "You already have an active loan. Please settle it before applying again.";
            header("Location: ../pages/capply_loan_page.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "No applicant profile found. Please complete your profile first.";
        header("Location: ../pages/capply_loan_page.php");
        exit();
    }

    // Validate form data
    $loanAmount = $_POST['loanAmount'] ?? null;
    $paymentTerm = $_POST['paymentTerm'] ?? null;
    $loanPurpose = $_POST['loanPurpose'] ?? null;
    $otherLoanPurpose = $_POST['otherLoanPurpose'] ?? null;

    // Validate required fields
    if (!$loanAmount || !$paymentTerm || !$loanPurpose) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../pages/capply_loan_page.php");
        exit();
    }

    // Loan Purpose
    if ($loanPurpose === 'Others' && !empty($otherLoanPurpose)) {
        $loanPurpose = $otherLoanPurpose;
    } elseif ($loanPurpose === 'Others' && empty($otherLoanPurpose)) {
        $_SESSION['error'] = "Please specify the loan purpose.";
        header("Location: ../pages/capply_loan_page.php");
        exit();
    }

    // Insert loan record
    $stmt = $pdo->prepare("INSERT INTO loan_info 
        (loanAmount, paymentTerm, loanPurpose, applicantID, status) 
        VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->execute([
        $loanAmount,
        $paymentTerm,
        $loanPurpose,
        $applicantID
    ]);

    // Set success message and redirect with success parameter
    $_SESSION['success'] = "Your loan application has been submitted successfully!";
    header("Location: ../pages/capply_loan_page.php?success=true");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: ../pages/capply_loan_page.php");
    exit();
}
?>