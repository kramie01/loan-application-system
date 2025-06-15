<?php
session_start();

// Get session data and clear it
$errors = [
    'login' => $_SESSION['login_error'] ?? null,
    'register' => $_SESSION['register_error'] ?? null,
    'register_success' => $_SESSION['register_success'] ?? null,
];
$activeForm = $_SESSION['active_form'] ?? 'role-selection';
$loginRole = $_SESSION['login_role'] ?? null;

// Clear session data
unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form'], $_SESSION['register_success'], $_SESSION['login_role']);

// Helper functions
function showMessage($message, $type = 'error') {
    return !empty($message) ? "<p class='{$type}-message'>{$message}</p>" : '';
}

function isActiveForm($formId, $activeForm) {
    return $formId === $activeForm ? 'active' : '';
}

// Check if account was deleted
$accountDeleted = isset($_GET['deleted']) && $_GET['deleted'] === 'true';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LendEase - Home Login</title>
    <link rel="stylesheet" href="../assets/css/home_style.css">
    <link rel="stylesheet" href="../assets/css/account_deleted_modal.css" />

</head>
<body data-login-role="<?= htmlspecialchars($loginRole ?? '') ?>">
    <div class="container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="icon">
                <img src="../assets/images/lendease_white.png" alt="LoanTite Logo">
            </div>
            <h1>LENDEASE</h1>
            <p>Get cash fast with a reliable loan service you can trust.</p>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <!-- Role Selection -->
            <div id="role-selection" class="form-container <?= isActiveForm('role-selection', $activeForm) ?>">
                <h2>Login as:</h2>
                <button onclick="showLogin('admin')">Admin</button>
                <button onclick="showLogin('client')">Client</button>
            </div>

            <!-- Login Form -->
            <div id="login" class="form-container <?= isActiveForm('login', $activeForm) ?>">
                <h2 id="login-title">Login</h2>
                <?= showMessage($errors['login']) ?>
                <?= showMessage($errors['register_success'], 'success') ?>
                
                <form action="../auth/login_register.php" method="POST">
                    <input type="hidden" name="role" id="login-role" value="<?= htmlspecialchars($loginRole ?? '') ?>">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                </form>
                
                <p id="register-link">Don't have an account? <a href="#" onclick="showRegister()">Register</a></p>
                <span class="back-link" onclick="showRoleSelection()">← Back</span>
            </div>

            <!-- Register Form -->
            <div id="register" class="form-container <?= isActiveForm('register', $activeForm) ?>">
                <h2>Register</h2>
                <?= showMessage($errors['register']) ?>
                
                <form action="../auth/login_register.php" method="POST">
                    <input type="hidden" name="role" value="client">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="repassword" placeholder="Re-enter Password" required>
                    <button type="submit" name="register">Register</button>
                </form>
                
                <span class="back-link" onclick="showLogin('client')">← Back to Login</span>
            </div>
        </div>
    </div>

        <!-- Account Deleted Success Modal -->
    <?php if ($accountDeleted): ?>
    <div id="accountDeletedModal" class="modal" style="display: block;">
        <div class="modal-content">
            <div class="modal-header success-header">
                <h4>✅ Account Successfully Deleted</h4>
            </div>
            <div class="modal-body">
                <div class="success-icon">🗑️</div>
                <h3>Your account has been permanently deleted</h3>
                <div class="deletion-summary">
                    <p>All your information has been successfully removed from our system:</p>
                    <ul>
                        <li>✓ Personal profile information</li>
                        <li>✓ Employment details</li>
                        <li>✓ Credit card information</li>
                        <li>✓ Loan applications and history</li>
                        <li>✓ User account and login credentials</li>
                    </ul>
                </div>
                <div class="farewell-message">
                    <p><strong>Thank you for using LendEase.</strong></p>
                    <p>If you decide to use our services again in the future, you'll need to create a new account and complete your profile.</p>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-primary" onclick="closeAccountDeletedModal()">CONTINUE TO HOMEPAGE</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="../assets/js/home_script.js"></script>
</body>
</html>
