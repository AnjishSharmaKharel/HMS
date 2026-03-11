<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);
include "config.php";

if (!isset($_SESSION["username"]) || !in_array($_SESSION["role"], ["admin", "staff"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET["delete"]) && isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "staff"])) {
    $id = intval($_GET["delete"]);
    $conn->query("DELETE FROM customer WHERE customerID=$id");
}
if (isset($_POST["update_customer"])) {
    $update_errors = [];
    $cid = intval($_POST["customerID"]);
    $name_raw = isset($_POST["customer_fullname"]) ? trim($_POST["customer_fullname"]) : "";
    $email_raw = isset($_POST["customer_email"]) ? trim($_POST["customer_email"]) : "";
    $phone_raw = isset($_POST["customer_phone"]) ? trim($_POST["customer_phone"]) : "";
    if ($name_raw === "") {
        $update_errors[] = "Full name is required.";
    }
    if (!filter_var($email_raw, FILTER_VALIDATE_EMAIL)) {
        $update_errors[] = "Valid email is required.";
    }
    if (empty($update_errors)) {
        $name = $conn->real_escape_string($name_raw);
        $email = $conn->real_escape_string($email_raw);
        $phone = $conn->real_escape_string($phone_raw);
        $conn->query("UPDATE customer SET customer_fullname='$name', customer_email='$email', customer_phone='$phone' WHERE customerID=$cid");
        $update_success = "Customer updated successfully.";
    }
}

if (isset($_POST["add_customer"])) {
    $add_errors = [];
    $add_success = "";
    $name_raw = isset($_POST["new_fullname"]) ? trim($_POST["new_fullname"]) : "";
    $email_raw = isset($_POST["new_email"]) ? trim($_POST["new_email"]) : "";
    $phone_raw = isset($_POST["new_phone"]) ? trim($_POST["new_phone"]) : "";
    $password_raw = isset($_POST["new_password"]) ? $_POST["new_password"] : "";
    if ($name_raw === "") {
        $add_errors[] = "Full name is required.";
    }
    if (!filter_var($email_raw, FILTER_VALIDATE_EMAIL)) {
        $add_errors[] = "Valid email is required.";
    }
    if (strlen($password_raw) < 8) {
        $add_errors[] = "Password must be at least 8 characters.";
    }
    if (empty($add_errors)) {
        $name = $conn->real_escape_string($name_raw);
        $email = $conn->real_escape_string($email_raw);
        $phone = $conn->real_escape_string($phone_raw);
        $password = $conn->real_escape_string($password_raw);
        $sql = "INSERT INTO customer (customer_fullname, customer_email, customer_phone, password) VALUES ('$name', '$email', '$phone', '$password')";
        if ($conn->query($sql)) {
            $add_success = "Customer added successfully.";
        } else {
            $add_errors[] = "Failed to add customer.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-brand">
                    LuxeHotel
                </a>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="dashboard.php"> Dashboard</a></li>
                    <li><a href="manage_rooms.php"> Manage Rooms</a></li>
                    <li><a href="manage_bookings.php"> Manage Bookings</a></li>
                    <li><a href="manage_staff.php"> Manage Staff</a></li>
                    <li><a href="manage_customer.php" class="acti've"> Manage Customers</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php">🚪 Logout</a>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <h2>Manage Customers</h2>
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
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Add New Customer</h3>
                    <?php
                        if (isset($add_errors) && !empty($add_errors)) {
                            echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #ef4444;color:#ef4444;background:rgba(239,68,68,.08);border-radius:.25rem;'>" . implode("<br>", array_map('htmlspecialchars', $add_errors)) . "</div>";
                        }
                        if (isset($add_success) && $add_success !== "") {
                            echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #10b981;color:#10b981;background:rgba(16,185,129,.08);border-radius:.25rem;'>" . htmlspecialchars($add_success) . "</div>";
                        }
                    ?>
                    <form method="POST" id="addCustomerForm">
                        <div class="form-group">
                            <label for="new_fullname">Full Name</label>
                            <input type="text" id="new_fullname" name="new_fullname" required>
                        </div>
                        <div class="form-group">
                            <label for="new_email">Email</label>
                            <input type="email" id="new_email" name="new_email" required>
                        </div>
                        <div class="form-group">
                            <label for="new_phone">Phone</label>
                            <input type="text" id="new_phone" name="new_phone">
                        </div>
                        <div class="form-group">
                            <label for="new_password">Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        <input type="submit" name="add_customer" value="Add Customer">
                    </form>
                    <script>
                        (function() {
                            const form = document.getElementById('addCustomerForm');
                            if (!form) return;
                            form.addEventListener('submit', function(e) {
                                let valid = true;
                                const name = document.getElementById('new_fullname');
                                const nameRegex = /^[a-zA-Z\s\-.']{2,}$/;
                                if (!nameRegex.test(name.value)) {
                                    alert("Full name must contain valid letters and be at least 2 characters.");
                                    valid = false;
                                }
                                const email = document.getElementById('new_email');
                                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                if (!emailRegex.test(email.value)) {
                                    alert("Please enter a valid email address.");
                                    valid = false;
                                }
                                const phone = document.getElementById('new_phone');
                                const phoneRegex = /^[0-9\-\+\s\(\)]{10,}$/;
                                if (phone.value && !phoneRegex.test(phone.value)) {
                                    alert("Please enter a valid phone number (digits, spaces, dashes).");
                                    valid = false;
                                }
                                const password = document.getElementById('new_password');
                                if (password.value.length < 8) {
                                    alert("Password must be at least 8 characters.");
                                    valid = false;
                                }
                                if (!valid) {
                                    e.preventDefault();
                                }
                            });
                        })();
                    </script>
                </div>
                <?php
                    $edit_customer = null;
                    if (isset($_GET["edit"])) {
                        $edit_id = intval($_GET["edit"]);
                        $res = $conn->query("SELECT customerID, customer_fullname, customer_email, customer_phone FROM customer WHERE customerID=$edit_id LIMIT 1");
                        if ($res && $res->num_rows === 1) {
                            $edit_customer = $res->fetch_assoc();
                        }
                    }
                ?>
                <?php if ($edit_customer) { ?>
                <div class="card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Edit Customer</h3>
                    <?php
                        if (isset($update_errors) && !empty($update_errors)) {
                            echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #ef4444;color:#ef4444;background:rgba(239,68,68,.08);border-radius:.25rem;'>" . implode("<br>", array_map('htmlspecialchars', $update_errors)) . "</div>";
                        }
                        if (isset($update_success) && $update_success !== "") {
                            echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #10b981;color:#10b981;background:rgba(16,185,129,.08);border-radius:.25rem;'>" . htmlspecialchars($update_success) . "</div>";
                        }
                    ?>
                    <form method="POST" id="editCustomerForm">
                        <input type="hidden" name="customerID" value="<?php echo htmlspecialchars($edit_customer["customerID"]); ?>">
                        <div class="form-group">
                            <label for="customer_fullname">Full Name</label>
                            <input type="text" id="customer_fullname" name="customer_fullname" value="<?php echo htmlspecialchars($edit_customer["customer_fullname"]); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_email">Email</label>
                            <input type="email" id="customer_email" name="customer_email" value="<?php echo htmlspecialchars($edit_customer["customer_email"]); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_phone">Phone</label>
                            <input type="text" id="customer_phone" name="customer_phone" value="<?php echo htmlspecialchars($edit_customer["customer_phone"]); ?>">
                        </div>
                        <input type="submit" name="update_customer" value="Update Customer">
                    </form>
                    <script>
                        const editForm = document.getElementById('editCustomerForm');
                        if (editForm) {
                        editForm.addEventListener('submit', function(e) {
                            let valid = true;
                            
                            // Name Validation
                            const name = document.getElementById('customer_fullname');
                            const nameRegex = /^[a-zA-Z\s\-.']{2,}$/;
                            if (!nameRegex.test(name.value)) {
                                alert("Full name must contain valid letters and be at least 2 characters.");
                                valid = false;
                            }

                            // Email Validation
                            const email = document.getElementById('customer_email');
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (!emailRegex.test(email.value)) {
                                alert("Please enter a valid email address.");
                                valid = false;
                            }

                            // Phone Validation (Optional)
                            const phone = document.getElementById('customer_phone');
                            const phoneRegex = /^[0-9\-\+\s\(\)]{10,}$/;
                            if (phone.value && !phoneRegex.test(phone.value)) {
                                alert("Please enter a valid phone number (digits, spaces, dashes).");
                                valid = false;
                            }

                            if (!valid) {
                                e.preventDefault();
                            }
                        });
                        }
                    </script>
                </div>
                <?php } ?>
                <div class="card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">All Customers</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Customer ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = $conn->query("SELECT customerID, customer_fullname, customer_email, customer_phone FROM customer ORDER BY customerID DESC");
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row["customerID"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["customer_fullname"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["customer_email"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["customer_phone"]) . "</td>";
                                echo "<td><a href='manage_customer.php?edit=" . htmlspecialchars($row["customerID"]) . "'>Edit</a> | <a href='manage_customer.php?delete=" . htmlspecialchars($row["customerID"]) . "' onclick=\"return confirm('Delete this customer?');\">Delete</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No customers found</td></tr>";
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
