<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);
include "config.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}



// Ensure staff table exists
$conn->query("CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(20) NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Add new staff
if (isset($_POST["add_staff"])) {
    $form_errors = [];
    $form_success = "";
    $name_raw = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $email_raw = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password_raw = isset($_POST["password"]) ? $_POST["password"] : "";
    $phone_raw = isset($_POST["phone"]) ? trim($_POST["phone"]) : "";
    if ($name_raw === "") {
        $form_errors[] = "Name is required.";
    }
    if (!filter_var($email_raw, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Valid email is required.";
    }
    if (strlen($password_raw) < 8) {
        $form_errors[] = "Password must be at least 8 characters.";
    }
    $email_check = $conn->query("SELECT id FROM staff WHERE email='" . $conn->real_escape_string($email_raw) . "' LIMIT 1");
    if ($email_check && $email_check->num_rows > 0) {
        $form_errors[] = "Email already exists.";
    }
    if (empty($form_errors)) {
        $name = $conn->real_escape_string($name_raw);
        $email = $conn->real_escape_string($email_raw);
        $password = password_hash($password_raw, PASSWORD_BCRYPT);
        $phone = $conn->real_escape_string($phone_raw);
        $sql = "INSERT INTO staff (name, email, password, phone, role) VALUES ('$name', '$email', '$password', '$phone', 'staff')";
        if ($conn->query($sql)) {
            $form_success = "Staff added successfully.";
        } else {
            $form_errors[] = "Failed to add staff.";
        }
    }
}

// Delete staff
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $conn->query("DELETE FROM staff WHERE id=$id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-brand">
                    <span style="font-size: 1.5rem;">🏨</span> LuxeHotel
                </a>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="dashboard.php">📊 Dashboard</a></li>
                    <li><a href="manage_rooms.php">🛏️ Manage Rooms</a></li>
                    <li><a href="manage_bookings.php">📅 Manage Bookings</a></li>
                    <li><a href="manage_staff.php" class="active">👥 Manage Staff</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php">🚪 Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-topbar">
                <h2>Manage Staff</h2>
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION["username"], 0, 1)); ?>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?php echo $_SESSION["username"]; ?></span>
                        <span class="user-role"><?php echo $_SESSION["role"]; ?></span>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <div class="card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Add New Staff</h3>
                    <?php
                    if (isset($form_errors) && !empty($form_errors)) {
                        echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #ef4444;color:#ef4444;background:rgba(239,68,68,.08);border-radius:.25rem;'>" . implode("<br>", array_map('htmlspecialchars', $form_errors)) . "</div>";
                    }
                    if (isset($form_success) && $form_success !== "") {
                        echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #10b981;color:#10b981;background:rgba(16,185,129,.08);border-radius:.25rem;'>" . htmlspecialchars($form_success) . "</div>";
                    }
                    ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" placeholder="e.g. Jane Doe" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="staff@example.com" required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$" title="Enter a valid email address">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter password" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" placeholder="+1 555 123 4567">
                        </div>
                        <input type="submit" name="add_staff" value="Add Staff">
                    </form>
                </div>

                <div class="card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">All Staff</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = $conn->query("SELECT id, name, email, phone, role FROM staff ORDER BY id DESC");
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";
                                echo "<td><span class='status-badge available'>" . htmlspecialchars($row["role"]) . "</span></td>";
                                echo "<td><a href='manage_staff.php?delete=" . $row["id"] . "' onclick=\"return confirm('Delete this staff member?');\">Delete</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No staff found</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
