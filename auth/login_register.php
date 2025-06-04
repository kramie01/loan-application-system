<?php
session_start();
require_once '../includes/config.php';

class AuthHandler {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function register($data) {
        $fullname = trim($data['fullname']);
        $username = trim($data['username']);
        $email = trim($data['email']);
        $password = $data['password'];
        $repassword = $data['repassword'];
        $role = 'client';
        
        // Validate passwords match
        if ($password !== $repassword) {
            $this->setError('register', "Passwords do not match!");
            return false;
        }
        
        // Check if user already exists
        if ($this->userExists($email, $username)) {
            $this->setError('register', "Email or Username already exists!");
            return false;
        }
        
        // Create new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users_t (fullname, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $username, $email, $hashedPassword, $role);
        
        if ($stmt->execute()) {
            $_SESSION['register_success'] = "Registration successful! Please log in.";
            $_SESSION['active_form'] = "login";
            $_SESSION['login_role'] = 'client';
            return true;
        }
        
        $this->setError('register', "Registration failed. Please try again.");
        return false;
    }
    
    public function login($data) {
        $email = trim($data['email']);
        $password = $data['password'];
        $role = $data['role'];
        
        $stmt = $this->conn->prepare("SELECT * FROM users_t WHERE email = ? AND role = ?");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $this->setUserSession($user);
                $this->redirectUser($user);
                return true;
            }
        }
        
        $this->setError('login', 'Invalid email, password, or role!');
        $_SESSION['login_role'] = $role;
        return false;
    }
    
    private function userExists($email, $username) {
        $stmt = $this->conn->prepare("SELECT email FROM users_t WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    
    private function setUserSession($user) {
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
    }
    
    private function redirectUser($user) {
        if ($user['role'] === 'client') {
            // Check if profile exists
            $stmt = $this->conn->prepare("SELECT applicantID FROM applicant_info WHERE emailAdrs = ?");
            $stmt->bind_param("s", $user['email']);
            $stmt->execute();
            $profileResult = $stmt->get_result();
            
            if ($profileResult->num_rows === 0) {
                $_SESSION['complete_profile_msg'] = "Please complete your profile.";
                header("Location: ../pages/ccomplete_profile_page.php");
            } else {
                $profile = $profileResult->fetch_assoc();
                $_SESSION['applicant_id'] = $profile['applicantID'];
                header("Location: ../pages/cdashboard_page.php");
            }
        } else {
            header("Location: ../pages/adashboard_page.php");
        }
        exit();
    }
    
    private function setError($type, $message) {
        $_SESSION["{$type}_error"] = $message;
        $_SESSION['active_form'] = $type;
    }
}

// Handle requests
$auth = new AuthHandler($conn);

if (isset($_POST['register'])) {
    $auth->register($_POST);
    header("Location: ../pages/home_page.php");
    exit();
}

if (isset($_POST['login'])) {
    if (!$auth->login($_POST)) {
        header("Location: ../pages/home_page.php");
        exit();
    }
}
?>
