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

    // ✅ Get email from session
    $emailAdrs = $_SESSION['email'] ?? null;

    if (!$emailAdrs) {
        echo "Email address not provided in session. Please log in again.";
        exit();
    }

    // Check if email already exists in applicant_info
    $stmt = $pdo->prepare("SELECT applicantID FROM applicant_info WHERE emailAdrs = ?");
    $stmt->execute([$emailAdrs]);
    $applicant = $stmt->fetch();

    if ($applicant) {
        $applicantID = $applicant['applicantID'];

        // Check for active loans
        $stmt = $pdo->prepare("SELECT status FROM loan_info WHERE applicantID = ? AND status NOT IN ('Paid', 'Cancelled')");
        $stmt->execute([$applicantID]);
        $activeLoan = $stmt->fetch();

        if ($activeLoan) {
            echo "You already have an active loan. Please settle it before applying again.";
            exit();
        }
    } else {
        echo "No applicant profile found. Please complete your profile first.";
        exit();
    }

    // ✅ Loan Purpose
    $loanPurpose = $_POST['loanPurpose'] ?? '';
    if ($loanPurpose === 'Others' && !empty($_POST['otherLoanPurpose'])) {
        $loanPurpose = $_POST['otherLoanPurpose'];
    }

    // ✅ Insert loan record
    $stmt = $pdo->prepare("INSERT INTO loan_info 
        (loanAmount, paymentTerm, loanPurpose, applicantID, status) 
        VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->execute([
        $_POST['loanAmount'],
        $_POST['paymentTerm'],
        $loanPurpose,
        $applicantID
    ]);

    header("Location: ../pages/loan_submitted_page.php");
    exit();
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}