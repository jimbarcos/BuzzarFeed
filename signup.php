<?php
/**
 * BuzzarFeed - Sign Up Page
 * 
 * User registration page for food enthusiasts and stall owners
 * 
 * @package BuzzarFeed
 * @version 1.0
 * @author BuzzarFeed Development Team
 * @date October 2025
 */

// Start session
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Page title for SEO
$pageTitle = "Sign Up - BuzzarFeed";
$pageDescription = "Create your BuzzarFeed account and start exploring food stalls or managing your own stall.";

// Handle form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $account_type = $_POST['account_type'] ?? '';
    $terms_agreed = isset($_POST['terms_agreed']);
    
    // Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    if (empty($account_type) || !in_array($account_type, ['enthusiast', 'owner'])) {
        $errors[] = "Please select an account type.";
    }
    
    if (!$terms_agreed) {
        $errors[] = "You must agree to the Terms of Service.";
    }
    
    // If no errors, process registration (would connect to database in production)
    if (empty($errors)) {
        // TODO: Hash password and save to database
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Save user to database...
        
        $success = true;
        // In production, redirect to login or dashboard
        // header('Location: login.php?registered=1');
        // exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=geist@400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/signup.css">
</head>
<body class="signup-page">
    <!-- Header Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Sign Up Section -->
    <section class="signup-section">
        <div class="container signup-container">
            <!-- Left Side - Polaroid Images -->
            <div class="signup-visual">
                <div class="polaroid-stack">
                    <div class="polaroid polaroid-signup-1">
                        <div class="polaroid-img"></div>
                    </div>
                    <div class="polaroid polaroid-signup-2">
                        <div class="polaroid-img"></div>
                    </div>
                    <div class="polaroid polaroid-signup-3">
                        <div class="polaroid-img"></div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Sign Up Form -->
            <div class="signup-form-wrapper">
                <a href="index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back
                </a>

                <h1 class="signup-title">Sign up</h1>
                <p class="signup-subtitle">
                    Already have an account? <a href="login.php" class="link-orange">Sign in</a>
                </p>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <p>Account created successfully! <a href="login.php">Sign in now</a></p>
                    </div>
                <?php endif; ?>

                <form action="signup.php" method="POST" class="signup-form" id="signupForm">
                    <!-- Name Field -->
                    <div class="form-group">
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-input" 
                            placeholder="Name"
                            value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                            required
                        >
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="form-input" 
                            placeholder="Email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required
                        >
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="form-input" 
                                placeholder="Password"
                                required
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="far fa-eye-slash" id="password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                name="confirm_password" 
                                id="confirm_password" 
                                class="form-input" 
                                placeholder="Confirm Password"
                                required
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="far fa-eye-slash" id="confirm_password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Account Type Selection -->
                    <div class="form-group">
                        <label class="form-label">Account type:</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input 
                                    type="radio" 
                                    name="account_type" 
                                    value="enthusiast"
                                    <?php echo (isset($_POST['account_type']) && $_POST['account_type'] === 'enthusiast') ? 'checked' : ''; ?>
                                    required
                                >
                                <span class="radio-label">
                                    <span class="radio-dot"></span>
                                    Food enthusiast
                                </span>
                            </label>
                            <label class="radio-option">
                                <input 
                                    type="radio" 
                                    name="account_type" 
                                    value="owner"
                                    <?php echo (isset($_POST['account_type']) && $_POST['account_type'] === 'owner') ? 'checked' : ''; ?>
                                >
                                <span class="radio-label">
                                    <span class="radio-dot"></span>
                                    Food stall owner
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="form-group">
                        <label class="checkbox-option">
                            <input 
                                type="checkbox" 
                                name="terms_agreed" 
                                id="terms_agreed"
                                <?php echo isset($_POST['terms_agreed']) ? 'checked' : ''; ?>
                                required
                            >
                            <span class="checkbox-label">
                                <span class="checkbox-box"></span>
                                I agree to all statements in <a href="terms.php" class="link-orange" target="_blank">Terms of Services</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-full btn-submit">
                        CREATE ACCOUNT
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
