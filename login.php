<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "hotel_db");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = isset($_GET["type"]) ? $_GET["type"] : "";
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";

    switch ($type) {
        case "admin":
             $sql = "SELECT * FROM admin
                    WHERE admin_email='$email'
                    AND password='$password'";
            $result = mysqli_query($connection, $sql);

            if (mysqli_num_rows($result) > 0) {
                $_SESSION["username"] = $email;
                $_SESSION["role"] = "admin";
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Invalid customer email or password";
            }
            break;
        case "staff":
            $sql = "SELECT * FROM staff
                    WHERE staff_email='$email'
                    AND password='$password'";
            $result = mysqli_query($connection, $sql);

            if (mysqli_num_rows($result) > 0) {
                $_SESSION["username"] = $email;
                $_SESSION["role"] = "staff";
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Invalid customer email or password";
            }
            break;

        case "customer":
            $sql = "SELECT * FROM customer
                    WHERE customer_email='$email'
                    AND password='$password'";
            $result = mysqli_query($connection, $sql);

            if (mysqli_num_rows($result) > 0) {
                $_SESSION["username"] = $email;
                $_SESSION["role"] = "customer";
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Invalid customer email or password";
            }
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxeHotel - Login & Register</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<nav>
    <div class="navbar-container">
        <a href="index.php" class="navbar-logo">
            <div class="navbar-logo-icon">LH</div>
            <span class="navbar-logo-text">LuxeHotel</span>
        </a>
        <a href="index.php" class="back-home-btn">← Back to Home</a>
    </div>
</nav>

<div class="login-wrapper">
    <div class="login-container">
        <div class="login-card">

            <div class="login-header">
                <div class="login-icon">🔐</div>
                <h1 class="login-title" id="pageTitle">Login</h1>
                <p class="login-subtitle" id="pageSubtitle">Choose your role</p>
            </div>

            <div class="role-tabs">
                <label class="role-tab active" onclick="switchRole('admin', this)">👨‍💼 Admin</label>
                <label class="role-tab" onclick="switchRole('staff', this)">👤 Staff</label>
                <label class="role-tab" onclick="switchRole('customer', this)">🧑‍🎓 Customer</label>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-alert">
                    <?php foreach ($errors as $error): ?>
                        <div class="error-item"><?= $error ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- ADMIN -->
            <div class="form-section active" id="adminSection">
                <form method="POST" action="login.php?type=admin">
                    <input type="email" name="email" class="form-input" placeholder="Admin Email" required>
                    <input type="password" name="password" class="form-input" placeholder="Password" required>
                    <button type="submit" class="form-submit">→ Sign In</button>
                </form>
            </div>

            <!-- STAFF -->
            <div class="form-section" id="staffSection">
                <form method="POST" action="login.php?type=staff">
                    <input type="email" name="email" class="form-input" placeholder="Staff Email" required>
                    <input type="password" name="password" class="form-input" placeholder="Password" required>
                    <button type="submit" class="form-submit">→ Sign In</button>
                </form>
            </div>

            <!-- CUSTOMER -->
            <div class="form-section" id="customerSection">
                <form method="POST" action="login.php?type=customer">
                    <input type="email" name="email" class="form-input" placeholder="Customer Email" required>
                    <input type="password" name="password" class="form-input" placeholder="Password" required>
                    <button type="submit" class="form-submit">→ Sign In</button>
                </form>
                <div class="login-footer">
                    <a href="register.php">Register as Customer</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function switchRole(role, el) {

    document.querySelectorAll('.role-tab')
        .forEach(tab => tab.classList.remove('active'));
    el.classList.add('active');

    document.querySelectorAll('.form-section')
        .forEach(section => section.classList.remove('active'));

    if (role === 'admin') {
        document.getElementById('adminSection').classList.add('active');
        pageTitle.innerText = "Admin Login";
        pageSubtitle.innerText = "Access admin panel";
    }
    else if (role === 'staff') {
        document.getElementById('staffSection').classList.add('active');
        pageTitle.innerText = "Staff Login";
        pageSubtitle.innerText = "Access staff panel";
    }
    else {
        document.getElementById('customerSection').classList.add('active');
        pageTitle.innerText = "Customer Login";
        pageSubtitle.innerText = "Book your stay";
    }
}
</script>

</body>
</html>
