<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "hotel_db");
// Handle registration form submission
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = isset($_POST["firstName"]) ? trim($_POST["firstName"]) : "";
    $lastName = isset($_POST["lastName"]) ? trim($_POST["lastName"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
    $confirmPassword = isset($_POST["confirmPassword"])
        ? trim($_POST["confirmPassword"])
        : "";
    $agreeTerms = isset($_POST["agreeTerms"]) ? true : false;

    // Validation
    if (empty($firstName)) {
        $errors[] = "First name is required";
    }
    if (empty($lastName)) {
        $errors[] = "Last name is required";
    }
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match('/^(97|98)\d{8}$/', $phone)) {
        $errors[] = "Invalid phone number";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif (
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[0-9]/", $password)
    ) {
        $errors[] =
            "Password must contain at least one uppercase letter and one number";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    if (!$agreeTerms) {
        $errors[] = "You must agree to the Terms of Service";
    }

    // If no errors, register user
    if (empty($errors)) {
        // In production, save to database
        // For now, just simulate successful registration
        $fullname = $firstName . " " . $lastName;

        // Create session for auto-login
        $_SESSION["username"] = $email;
        $_SESSION["name"] = $fullname;

        $_SESSION["role"] = "customer";
        $_SESSION["logged_in"] = true;
        $_SESSION["login_time"] = date("Y-m-d H:i:s");
        $query = "INSERT INTO customer (customer_fullname, customer_email, customer_phone, password)
                  VALUES ('$fullname', '$email', '$phone', '$password')";

        if (mysqli_query($connection, $query)) {
            // Redirect to customer dashboard
            header("Location: index.php");
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxeHotel - Customer Registration</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="navbar-container">
            <a href="index.php" class="navbar-logo">
                <div class="navbar-logo-icon">LH</div>
                <span class="navbar-logo-text">LuxeHotel</span>
            </a>
            <div class="navbar-nav">
                <a href="index.php" class="back-home-btn">← Back to Home</a>
            </div>
        </div>
    </nav>

    <!-- Register Container -->
    <div class="register-wrapper">
        <div class="register-container">
            <div class="register-card">
                <!-- Header -->
                <div class="register-header">
                    <div class="register-icon">📝</div>
                    <h1 class="register-title">Create Account</h1>
                    <p class="register-subtitle">Join LuxeHotel and book your stay</p>
                </div>

                <!-- Form -->
                <form method="POST" action="register.php" class="register-form" id="registerForm">
                    <!-- Error Messages -->
                    <?php if (!empty($errors)): ?>
                    <div class="error-alert">
                        <div class="error-icon">⚠️</div>
                        <div class="error-list">
                            <?php foreach ($errors as $error): ?>
                            <div class="error-item"><?php echo htmlspecialchars(
                                $error,
                            ); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div id="clientErrors" class="error-alert" style="display:none;">
                        <div class="error-icon">⚠️</div>
                        <div class="error-list" id="clientErrorList"></div>
                    </div>

                    <!-- First Name & Last Name -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="firstName">First Name</label>
                            <input
                                type="text"
                                id="firstName"
                                name="firstName"
                                class="form-input"
                                placeholder="John"
                                value="<?php echo isset($_POST["firstName"])
                                    ? htmlspecialchars($_POST["firstName"])
                                    : ""; ?>"
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="lastName">Last Name</label>
                            <input
                                type="text"
                                id="lastName"
                                name="lastName"
                                class="form-input"
                                placeholder="Doe"
                                value="<?php echo isset($_POST["lastName"])
                                    ? htmlspecialchars($_POST["lastName"])
                                    : ""; ?>"
                                required
                            >
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="you@example.com"
                            value="<?php echo isset($_POST["email"])
                                ? htmlspecialchars($_POST["email"])
                                : ""; ?>"
                            required
                        >
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-input"
                            placeholder="+977 xxx-xxx-xxxx"
                            value="<?php echo isset($_POST["phone"])
                                ? htmlspecialchars($_POST["phone"])
                                : ""; ?>"
                            required
                        >
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="••••••••"
                            required
                            onkeyup="checkPasswordStrength()"
                        >
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div id="strengthFill" class="strength-fill"></div>
                            </div>
                            <div class="strength-text">
                                <span id="strengthText">Password must be 8+ characters with uppercase and numbers</span>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label" for="confirmPassword">Confirm Password</label>
                        <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            class="form-input"
                            placeholder="••••••••"
                            required
                        >
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="form-checkbox">
                        <input
                            type="checkbox"
                            id="agreeTerms"
                            name="agreeTerms"
                            class="checkbox-input"
                            required
                        >
                        <label for="agreeTerms" class="checkbox-label">
                            I agree to the <a href="#" onclick="return false;">Terms of Service</a> and <a href="#" onclick="return false;">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="form-submit">
                        <span>✓</span>
                        Create Account
                    </button>
                </form>

                <!-- Footer -->
                <div class="register-footer">
                    Already have an account? <a href="login.php">Sign In</a>
                </div>
            </div>
        </div>
    </div>

    <script src="register.js"></script>
</body>
</html>
