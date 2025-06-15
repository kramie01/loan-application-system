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
    
    // Start transaction for data integrity
    $pdo->beginTransaction();
    
    $emailAdrs = $_SESSION['email'];
    
    // Get applicant ID first
    $stmt = $pdo->prepare("SELECT applicantID FROM applicant_info WHERE emailAdrs = ?");
    $stmt->execute([$emailAdrs]);
    $applicant = $stmt->fetch();
    
    if ($applicant) {
        $applicantID = $applicant['applicantID'];
        
        // Delete in proper order to maintain referential integrity
        
        // 1. Delete credit card information
        $stmt = $pdo->prepare("DELETE FROM creditcard_info WHERE applicantID = ?");
        $stmt->execute([$applicantID]);
        
        // 2. Delete loan information
        $stmt = $pdo->prepare("DELETE FROM loan_info WHERE applicantID = ?");
        $stmt->execute([$applicantID]);
        
        // 3. Get TIN number for employment deletion
        $stmt = $pdo->prepare("SELECT tinNumber FROM applicant_info WHERE applicantID = ?");
        $stmt->execute([$applicantID]);
        $applicantData = $stmt->fetch();
        
        if ($applicantData && $applicantData['tinNumber']) {
            // 4. Delete employment information
            $stmt = $pdo->prepare("DELETE FROM employment_info WHERE tinNumber = ?");
            $stmt->execute([$applicantData['tinNumber']]);
        }
        
        // 5. Delete applicant information
        $stmt = $pdo->prepare("DELETE FROM applicant_info WHERE applicantID = ?");
        $stmt->execute([$applicantID]);
    }

    // 6. Finally, delete user account
    $stmt = $pdo->prepare("DELETE FROM users_t WHERE email = ?");
    $stmt->execute([$emailAdrs]);
    
    // Commit the transaction
    $pdo->commit();
    
    // Destroy session
    session_destroy();
    
    // Redirect to home page with success message
    header('Location: ../pages/home_page.php?deleted=true');
    exit();
    
} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    // Log the error
    error_log("Account deletion failed for user: " . $emailAdrs . " - Error: " . $e->getMessage());
    
    $_SESSION['error'] = "An error occurred while deleting your account. Please try again or contact support.";
    header('Location: ../pages/cprofile_page.php');
    exit();
}
?>