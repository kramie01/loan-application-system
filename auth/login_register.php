<?php
session_start();
require_once '../includes/config.php';

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $role = 'client';

    // Password check
    if ($password !== $repassword) {
        $_SESSION['register_error'] = "Passwords do not match!";
        $_SESSION['active_form'] = "register";
        header("Location: ../pages/home_page.php");
        exit();
    }

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT email FROM users_t WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = "Email or Username already exists!";
        $_SESSION['active_form'] = "register";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users_t (fullname, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $username, $email, $hashedPassword, $role);
        $stmt->execute();

        $_SESSION['register_success'] = "Registration successful! Please log in.";
        $_SESSION['active_form'] = "login";
        $_SESSION['login_role'] = 'client';
    }

    header("Location: ../pages/home_page.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT * FROM users_t WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'client') {
            // Check if profile exists in applicant_info table by matching emailAdrs with user's email
            $stmt = $conn->prepare("SELECT applicantID FROM applicant_info WHERE emailAdrs = ?");
            $stmt->bind_param("s", $user['email']);
            $stmt->execute();
            $profileResult = $stmt->get_result();

            if ($profileResult->num_rows === 0) {
                // No profile found, ask user to complete profile
                $_SESSION['complete_profile_msg'] = "Please complete your profile.";
                header("Location: ../pages/ccomplete_profile_page.php");
                exit();
            } else {
                // Profile found, save applicantID in session for later use
                $profile = $profileResult->fetch_assoc();
                $_SESSION['applicant_id'] = $profile['applicantID'];
                header("Location: ../pages/cdashboard_page.php");
                exit();
            }
        } else {
            // For admin or other roles, redirect accordingly
            header("Location: ../pages/adashboard_page.php");
            exit();
        }
    }
}

    $_SESSION['login_error'] = 'Invalid email, password, or role!';
    $_SESSION['active_form'] = 'login';
    $_SESSION['login_role'] = $role;
    header("Location: ../pages/home_page.php");
    exit();
}