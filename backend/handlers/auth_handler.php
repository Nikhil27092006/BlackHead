<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

// Debug Log
$logFile = 'auth_debug.log';
function debugLog($msg) {
    // Disabled in production
}

debugLog("Received request: " . $_SERVER['REQUEST_METHOD'] . " | Action: " . ($_POST['action'] ?? 'none'));
if(isset($_POST['redirect'])) debugLog("Redirect param: " . $_POST['redirect']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'register') {
        $name = clean($_POST['name']);
        $email = clean($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        debugLog("Register attempt: Name: $name, Email: $email");

        $redirect_param = isset($_POST['redirect']) ? '&redirect=' . clean($_POST['redirect']) : '';
        debugLog("Processing registration for: $email");
        
        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            debugLog("Validation failed: Empty fields");
            $_SESSION['error'] = "All fields are required.";
            redirect_to("../../index.php?page=register" . $redirect_param);
        }

        if ($password !== $confirm_password) {
            debugLog("Validation failed: Passwords do not match");
            $_SESSION['error'] = "Passwords do not match.";
            redirect_to("../../index.php?page=register" . $redirect_param);
        }

        if (strlen($password) < 8) {
            debugLog("Validation failed: Password too short");
            $_SESSION['error'] = "Password must be at least 8 characters.";
            redirect_to("../../index.php?page=register" . $redirect_param);
        }

        // Check if email exists
        debugLog("Checking if email exists: $email");
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            debugLog("Email already registered: $email");
            $_SESSION['error'] = "Email already registered.";
            redirect_to("../../index.php?page=register" . $redirect_param);
        }

        debugLog("Hashing password and inserting user...");
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password]);
            
            $user_id = $pdo->lastInsertId();
            debugLog("Registration successful! User ID: $user_id");

            // Auto-login after registration
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            debugLog("Migrating cart for user $user_id...");
            // Migrate cart
            migrateCart($pdo, session_id(), $user_id);
            
            $_SESSION['success'] = "Welcome to BLACKHEAD! You have successfully signed up. You can start shopping now!";
            
            $redirect = isset($_POST['redirect']) ? clean($_POST['redirect']) : 'home';
            debugLog("Finalizing registration. Redirecting to: $redirect");
            redirect_to("../../index.php?page=" . $redirect);
        } catch (PDOException $e) {
            debugLog("Registration error: " . $e->getMessage());
            $_SESSION['error'] = "Something went wrong. Please try again.";
            redirect_to("../../index.php?page=register" . $redirect_param);
        }
    }

    if ($action == 'login') {
        $email = clean($_POST['email']);
        $password = $_POST['password'];

        $redirect_param = isset($_POST['redirect']) ? '&redirect=' . clean($_POST['redirect']) : '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Please fill in all fields.";
            redirect_to("../../index.php?page=login" . $redirect_param);
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] == 'blocked') {
                $_SESSION['error'] = "Your account has been blocked. Please contact support.";
                redirect_to("../../index.php?page=login" . $redirect_param);
            }

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            // Migrate cart
            migrateCart($pdo, session_id(), $user['id']);

            $redirect = isset($_POST['redirect']) ? clean($_POST['redirect']) : 'home';
            debugLog("Login successful for: $email | Redirecting to: $redirect");
            redirect_to("../../index.php?page=" . $redirect);
        } else {
            debugLog("Login failed for: $email");
            $_SESSION['error'] = "Invalid email or password.";
            redirect_to("../../index.php?page=login" . $redirect_param);
        }
    }

    if ($action == 'forgot_password') {
        $email = clean($_POST['email']);
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // In a real app, send actual email. For now, simulate success.
            $_SESSION['success'] = "Password reset link sent to your email!";
            header("Location: " . SITE_URL . "index.php?page=login");
        } else {
            $_SESSION['error'] = "Email address not found.";
            header("Location: " . SITE_URL . "index.php?page=forgot_password");
        }
        exit;
    }

    if ($action == 'verify_otp') {
        $otp = clean($_POST['otp']);
        
        // Security: Check against session-stored OTP instead of hardcoded '123456'
        $storedOtp = $_SESSION['simulated_otp'] ?? '123456'; 
        if ($otp === $storedOtp) {
            $_SESSION['success'] = "Verified successfully! Welcome back.";
            // For phone login simulation, log them in immediately
            if (isset($_SESSION['phone_for_otp'])) {
                $_SESSION['user_id'] = 1; // Simulated ID
                $_SESSION['user_name'] = "Phone User";
                $_SESSION['user_email'] = $_SESSION['phone_for_otp'];
                
                // Migrate cart
                migrateCart($pdo, session_id(), 1);
                
                unset($_SESSION['phone_for_otp']);
                
                $redirect = isset($_POST['redirect']) ? clean($_POST['redirect']) : 'home';
                session_write_close();
                redirect_to("../../index.php?page=" . $redirect);
            } else {
                redirect_to("../../index.php?page=login");
            }
        } else {
            $_SESSION['error'] = "Invalid verification code.";
            redirect_to("../../index.php?page=verify_otp" . (isset($_POST['redirect']) ? '&redirect=' . clean($_POST['redirect']) : ''));
        }
        exit;
    }
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action == 'resend_otp') {
        $newOtp = (string)rand(100000, 999999);
        $_SESSION['simulated_otp'] = $newOtp;
        $_SESSION['success'] = "Your new SIMULATION code is " . $newOtp;
        $redirect = isset($_GET['redirect']) ? '&redirect=' . clean($_GET['redirect']) : '';
        header("Location: ../../index.php?page=verify_otp" . $redirect);
        exit;
    }
} else {
    header("Location: ../../index.php");
    exit;
}
?>
