<?php
session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? null,
    'register' => $_SESSION['register_error'] ?? null,
    'register_success' => $_SESSION['register_success'] ?? null,
];
$activeForm = $_SESSION['active_form'] ?? 'role-selection';
$loginRole = $_SESSION['login_role'] ?? null;

unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form'], $_SESSION['register_success'], $_SESSION['login_role']);

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function showSuccess($msg) {
    return !empty($msg) ? "<p class='success-message'>$msg</p>" : '';
}

function isActiveForm($formId, $activeForm) {
    return $formId === $activeForm ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
  
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cashalo - Home Login</title>
  <link rel="stylesheet" href="../assets/css/home_style.css" />
</head>

<div class="container">
  <!-- Left Panel -->
  <div class="left-panel">
    <div class="icon">
      <img src="../assets/images/lendease_white.png" alt="LoanTite Logo">
    </div>
    <h1>CASHALO</h1>
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
      <?= showError($errors['login']) ?>
      <?= showSuccess($errors['register_success']) ?>
      <form action="../auth/login_register.php" method="POST" id="login-form">
        <input type="hidden" name="role" id="login-role" value="<?= htmlspecialchars($loginRole ?? '') ?>" />
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">Login</button>
      </form>
      <p id="register-link" style="margin-top: 10px;">Don't have an account? <a href="#" onclick="showRegister()">Register</a></p>
      <span class="back-link" onclick="showRoleSelection()">&#8592; Back</span>
    </div>

    <!-- Register Form -->
    <div id="register" class="form-container <?= isActiveForm('register', $activeForm) ?>">
      <h2>Register</h2>
      <?= showError($errors['register']) ?>
      <form action="../auth/login_register.php" method="POST" id="register-form">
        <input type="hidden" name="role" value="client" />
        <input type="text" name="fullname" placeholder="Full Name" required><br>
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="repassword" placeholder="Re-enter Password" required><br>
        <button type="submit" name="register">Register</button>
      </form>
      <span class="back-link" onclick="showLogin('client')">&#8592; Back to Login</span>
    </div>
  </div> 
</div> 

<script>
  document.addEventListener("DOMContentLoaded", function () {
    document.body.setAttribute('data-login-role', '<?= $loginRole ?? '' ?>');
  });
</script>
<script src="../assets/js/home_script.js" defer></script>

</body>
</html>