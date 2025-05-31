<?php
session_start();
if (!isset($_SESSION['email'])) {
  header('Location: ../pages/home_page.php');
  exit();
}

require '../config.php'; // Database connection

$loan = null;
$employment = null;
$creditCards = [];

try {
  // Get the logged-in user's email
  $email = $_SESSION['email'];

  // Fetch the applicant ID
  $stmt = $pdo->prepare("SELECT id FROM applicant_info WHERE emailAdrs = ?");
  $stmt->execute([$email]);
  $applicant = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($applicant) {
    $applicantID = $applicant['id'];

    // Fetch loan information
    $stmt = $pdo->prepare("SELECT * FROM loan_info WHERE applicantID = ?");
    $stmt->execute([$applicantID]);
    $loan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch employment information
    $stmt = $pdo->prepare("SELECT * FROM employment_info WHERE applicantID = ?");
    $stmt->execute([$applicantID]);
    $employment = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch credit card information
    $stmt = $pdo->prepare("SELECT * FROM credit_card_info WHERE applicantID = ?");
    $stmt->execute([$applicantID]);
    $creditCards = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
} catch (PDOException $e) {
  echo "Database error: " . $e->getMessage();
}
?>
