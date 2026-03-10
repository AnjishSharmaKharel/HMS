<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);
include "config.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}



$conn=mysqli_connect("localhost","root","","hotel_db");

// Add new staff
if (isset($_POST["add_staff"])) {
    $form_errors = [];
    $form_success = "";
    $username_raw = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $name_raw = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $email_raw = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password_raw = isset($_POST["password"]) ? $_POST["password"] : "";
    if ($username_raw === "") {
        $form_errors[] = "Username is required.";
    }
    if ($name_raw === "") {
        $form_errors[] = "Name is required.";
    }
    if (!filter_var($email_raw, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Valid email is required.";
    }
    if (strlen($password_raw) < 8) {
        $form_errors[] = "Password must be at least 8 characters.";
    }
    $email_check = $conn->query("SELECT staff_id FROM staff WHERE staff_email='" . $conn->real_escape_string($email_raw) . "' LIMIT 1");
    if ($email_check && $email_check->num_rows > 0) {
        $form_errors[] = "Email already exists.";
    }
    if (empty($form_errors)) {
        $username = $conn->real_escape_string($username_raw);
        $name = $conn->real_escape_string($name_raw);
        $email = $conn->real_escape_string($email_raw);
        $password = $conn->real_escape_string($password_raw);
        $sql = "INSERT INTO staff (staff_username, staff_name, staff_email, password) VALUES ('$username', '$name', '$email', '$password')";
        if ($conn->query($sql)) {
            $form_success = "Staff added successfully.";
        } else {
            $form_errors[] = "Failed to add staff.";
        }
    }
}

// Update staff details
if (isset($_POST["update_staff"])) {
    $update_errors = [];
    $update_success = "";
    $staff_id = isset($_POST["staff_id"]) ? intval($_POST["staff_id"]) : 0;
    $username_raw = isset($_POST["edit_username"]) ? trim($_POST["edit_username"]) : "";
    $name_raw = isset($_POST["edit_name"]) ? trim($_POST["edit_name"]) : "";
    $email_raw = isset($_POST["edit_email"]) ? trim($_POST["edit_email"]) : "";
    $password_raw = isset($_POST["edit_password"]) ? $_POST["edit_password"] : "";
    if ($staff_id <= 0) {
        $update_errors[] = "Invalid staff ID.";
    }
    if ($username_raw === "") {
        $update_errors[] = "Username is required.";
    }
    if ($name_raw === "") {
        $update_errors[] = "Name is required.";
    }
    if (!filter_var($email_raw, FILTER_VALIDATE_EMAIL)) {
        $update_errors[] = "Valid email is required.";
    }
    $email_safe = $conn->real_escape_string($email_raw);
    $email_check = $conn->query("SELECT staff_id FROM staff WHERE staff_email='$email_safe' AND staff_id<>$staff_id LIMIT 1");
    if ($email_check && $email_check->num_rows > 0) {
        $update_errors[] = "Email already exists.";
    }
    if (!empty($password_raw) && strlen($password_raw) < 8) {
        $update_errors[] = "Password must be at least 8 characters.";
    }
    if (empty($update_errors)) {
        $username = $conn->real_escape_string($username_raw);
        $name = $conn->real_escape_string($name_raw);
        $email = $conn->real_escape_string($email_raw);
        if ($password_raw !== "") {
            $password = $conn->real_escape_string($password_raw);
            $sql = "UPDATE staff SET staff_username='$username', staff_name='$name', staff_email='$email', password='$password' WHERE staff_id=$staff_id";
        } else {
            $sql = "UPDATE staff SET staff_username='$username', staff_name='$name', staff_email='$email' WHERE staff_id=$staff_id";
        }
        if ($conn->query($sql)) {
            $update_success = "Staff updated successfully.";
        } else {
            $update_errors[] = "Failed to update staff.";
        }
    }
}

// Delete staff
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $conn->query("DELETE FROM staff WHERE staff_id=$id");
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
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" placeholder="e.g. RamSharma" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" placeholder="e.g. Ram Sharma" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="staff@example.com" required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$" title="Enter a valid email address">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter password" required>
                            <small id="passwordError" style="color: red; display: none;">Password must be at least 8 characters long.</small>
                        </div>
                    
                        <input type="submit" name="add_staff" value="Add Staff">
                    </form>
                </div>

                <script>
                    document.querySelector('form').addEventListener('submit', function(e) {
                        let valid = true;
                        
                        // Username Validation
                        const username = document.getElementById('username');
                        const usernameRegex = /^[a-zA-Z0-9_]{3,}$/;
                        if (!usernameRegex.test(username.value)) {
                            alert("Username must be at least 3 characters and alphanumeric.");
                            valid = false;
                        }

                        // Name Validation
                        const name = document.getElementById('name');
                        const nameRegex = /^[a-zA-Z\s\-.']{2,}$/;
                        if (!nameRegex.test(name.value)) {
                            alert("Name must contain valid letters and be at least 2 characters.");
                            valid = false;
                        }

                        // Email Validation
                        const email = document.getElementById('email');
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(email.value)) {
                            alert("Please enter a valid email address.");
                            valid = false;
                        }

                        // Password Validation
                        const password = document.getElementById('password');
                        if (password.value.length < 8) {
                            document.getElementById('passwordError').style.display = 'block';
                            valid = false;
                        } else {
                            document.getElementById('passwordError').style.display = 'none';
                        }

                        if (!valid) {
                            e.preventDefault();
                        }
                    });
                </script>

                <div class="card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">All Staff</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = $conn->query("SELECT staff_id, staff_username, staff_name, staff_email FROM staff ORDER BY staff_id DESC");
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row["staff_id"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["staff_username"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["staff_name"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["staff_email"]) . "</td>";
                                echo "<td style='display:flex;gap:.5rem;'>";
                                echo "<a href='manage_staff.php?edit=" . htmlspecialchars($row["staff_id"]) . "'>Edit</a>";
                                echo "<a href='manage_staff.php?delete=" . htmlspecialchars($row["staff_id"]) . "' onclick=\"return confirm('Delete this staff member?');\">Delete</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No staff found</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                
                <?php
                if (isset($_GET["edit"])) {
                    $edit_id = intval($_GET["edit"]);
                    $edit_res = $conn->query("SELECT staff_id, staff_username, staff_name, staff_email FROM staff WHERE staff_id=$edit_id LIMIT 1");
                    if ($edit_res && $edit_res->num_rows === 1) {
                        $edit_row = $edit_res->fetch_assoc();
                        echo "<div class=\"card\" style=\"background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top:2rem;\">";
                        echo "<h3 style=\"margin-bottom: 1.5rem; color: var(--primary);\">Edit Staff</h3>";
                        if (isset($update_errors) && !empty($update_errors)) {
                            echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #ef4444;color:#ef4444;background:rgba(239,68,68,.08);border-radius:.25rem;'>" . implode("<br>", array_map('htmlspecialchars', $update_errors)) . "</div>";
                        }
                        if (isset($update_success) && $update_success !== "") {
                            echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #10b981;color:#10b981;background:rgba(16,185,129,.08);border-radius:.25rem;'>" . htmlspecialchars($update_success) . "</div>";
                        }
                        echo "<form method='POST'>";
                        echo "<input type='hidden' name='staff_id' value='" . htmlspecialchars($edit_row["staff_id"]) . "'>";
                        echo "<div class='form-group'><label for='edit_username'>Username</label><input type='text' id='edit_username' name='edit_username' value='" . htmlspecialchars($edit_row["staff_username"]) . "' required></div>";
                        echo "<div class='form-group'><label for='edit_name'>Full Name</label><input type='text' id='edit_name' name='edit_name' value='" . htmlspecialchars($edit_row["staff_name"]) . "' required></div>";
                        echo "<div class='form-group'><label for='edit_email'>Email</label><input type='email' id='edit_email' name='edit_email' value='" . htmlspecialchars($edit_row["staff_email"]) . "' required></div>";
                        echo "<div class='form-group'><label for='edit_password'>Password (leave blank to keep unchanged)</label><input type='password' id='edit_password' name='edit_password' placeholder='New password'></div>";
                        echo "<input type='submit' name='update_staff' value='Update Staff'>";
                        echo "</form>";
                        echo "<script>
                            (function(){
                                const form = document.querySelector('form[method=\"POST\"] input[name=\"update_staff\"]') ? document.querySelector('form[method=\"POST\"]').closest('form') : null;
                            })();
                        </script>";
                        echo "<script>
                            document.querySelector('form').addEventListener('submit', function(e) {
                                if (!document.querySelector('input[name=\"update_staff\"]')) return;
                                let valid = true;
                                const username = document.getElementById('edit_username');
                                const usernameRegex = /^[a-zA-Z0-9_]{3,}$/;
                                if (!usernameRegex.test(username.value)) {
                                    alert('Username must be at least 3 characters and alphanumeric.');
                                    valid = false;
                                }
                                const name = document.getElementById('edit_name');
                                const nameRegex = /^[a-zA-Z\\s\\-\\.\\']{2,}$/;
                                if (!nameRegex.test(name.value)) {
                                    alert('Name must contain valid letters and be at least 2 characters.');
                                    valid = false;
                                }
                                const email = document.getElementById('edit_email');
                                const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
                                if (!emailRegex.test(email.value)) {
                                    alert('Please enter a valid email address.');
                                    valid = false;
                                }
                                const password = document.getElementById('edit_password');
                                if (password.value && password.value.length < 8) {
                                    alert('Password must be at least 8 characters.');
                                    valid = false;
                                }
                                if (!valid) {
                                    e.preventDefault();
                                }
                            });
                        </script>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>
