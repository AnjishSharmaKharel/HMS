<?php
session_start();

include "config.php";

error_reporting(0);
ini_set('display_errors', 0);


if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Update booking status
if (isset($_POST["update"]) && in_array($_SESSION["role"], ["admin", "staff"])) {
    $id = $_POST["booking_id"];
    $status = $_POST["status"];
    $conn->query("UPDATE bookings SET status='$status' WHERE id=$id");
    
    // Update room status based on booking status
    $booking = $conn->query("SELECT room_id FROM bookings WHERE id=$id")->fetch_assoc();
    if ($booking) {
        $room_id = $booking['room_id'];
        $room_status = ($status == 'cancelled') ? 'available' : 'booked';
        $conn->query("UPDATE rooms SET status='$room_status' WHERE id=$room_id");
    }
}

// Fetch Stats
$total_bookings = 0;
$pending_bookings = 0;
$total_rooms = 0;
$available_rooms = 0;
$total_customers = 0;

if (in_array($_SESSION["role"], ["admin", "staff"])) {
    $total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
    $pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status='pending'")->fetch_assoc()['count'];
    $total_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms")->fetch_assoc()['count'];
    $available_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms WHERE status='available'")->fetch_assoc()['count'];
    $total_customers = $conn->query("SELECT COUNT(*) as count FROM customer")->fetch_assoc()['count'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
    
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
                    <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "staff") { ?>
                        <li><a href="manage_rooms.php">🛏️ Manage Rooms</a></li>
                    <?php } ?>
                    <?php if (isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "staff"])) { ?>
                    <li><a href="manage_bookings.php">📅 Manage Bookings</a></li>
                    <?php } ?>
                    <?php if (isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin","staff"])) { ?>
                        <li><a href="manage_customer.php">🧑‍💼 Manage Customers</a></li>
                    <?php } ?>
                    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") { ?>
                        <li><a href="manage_staff.php">👥 Manage Staff</a></li>
                    <?php } ?>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php">🚪 Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-topbar">
                <h2>Dashboard Overview</h2>
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION["username"], 0, 1)); ?>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?php echo $_SESSION["name"]; ?></span>
                        <span class="user-role"><?php echo $_SESSION["role"]; ?></span>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <?php if (in_array($_SESSION["role"], ["admin", "staff"])) { ?>
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-card-title">Total Bookings</div>
                        <div class="stat-card-value"><?php echo $total_bookings; ?></div>
                    </div>
                    <div class="stat-card pending">
                        <div class="stat-card-title">Pending Bookings</div>
                        <div class="stat-card-value"><?php echo $pending_bookings; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-title">Total Rooms</div>
                        <div class="stat-card-value"><?php echo $total_rooms; ?></div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-card-title">Available Rooms</div>
                        <div class="stat-card-value"><?php echo $available_rooms; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-title">Total Customers</div>
                        <div class="stat-card-value"><?php echo $total_customers; ?></div>
                    </div>
                </div>
                <?php } ?>

                <h2>Quick Actions</h2>
                <div class="dashboard-actions">
                    <?php if (isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "staff"])) { ?>
                        <a href="manage_rooms.php" class="action-card">
                            <div class="action-icon">🛏️</div>
                            <div class="action-info">
                                <h3>Manage Rooms</h3>
                                <p>Add, edit, or delete rooms</p>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") { ?>
                        <a href="manage_bookings.php" class="action-card">
                            <div class="action-icon">📅</div>
                            <div class="action-info">
                                <h3>Manage Bookings</h3>
                                <p>View and update booking status</p>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") { ?>
                        <a href="manage_staff.php" class="action-card">
                            <div class="action-icon">👥</div>
                            <div class="action-info">
                                <h3>Manage Staff</h3>
                                <p>Add or remove staff members</p>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if (isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "staff"])) { ?>
                        <a href="manage_customer.php" class="action-card">
                            <div class="action-icon">🧑‍💼</div>
                            <div class="action-info">
                                <h3>Manage Customers</h3>
                                <p>View customer list and details</p>
                            </div>
                        </a>
                    <?php } ?>
                </div>

                <?php if (isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "staff"])) { ?>
                <h2>Rooms Overview</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $roomsRes = $conn->query("SELECT id, room_type, price, status FROM rooms ORDER BY id DESC LIMIT 5");
                    if ($roomsRes && $roomsRes->num_rows > 0) {
                        while ($r = $roomsRes->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($r["id"]) . "</td>";
                            echo "<td>" . htmlspecialchars($r["room_type"]) . "</td>";
                            echo "<td>" . htmlspecialchars($r["price"]) . "</td>";
                            echo "<td><span class='status-badge " . htmlspecialchars($r["status"]) . "'>" . ucfirst(htmlspecialchars($r["status"])) . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No rooms found</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <div style="text-align: right; margin-top: 1rem;">
                    <a href="manage_rooms.php" class="btn-primary" style="text-decoration: none; padding: 0.5rem 1rem;">View All Rooms &rarr;</a>
                </div>
                <?php } ?>

                <h2><?php echo ($_SESSION["role"] == "customer") ? "My Recent Bookings" : "Recent Bookings"; ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Room ID</th>
                            <th>Status</th>
                            <?php if (in_array($_SESSION["role"], ["admin", "staff"])) { ?>
                            <th>Action</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (in_array($_SESSION["role"], ["admin", "staff"])) {
                        $result = $conn->query("SELECT * FROM bookings ORDER BY id DESC LIMIT 5");
                    } else {
                        // For customers, show their own bookings
                        $email = $conn->real_escape_string($_SESSION["username"]);
                        $result = $conn->query("SELECT * FROM bookings WHERE customer_email='$email' ORDER BY id DESC LIMIT 5");
                    }

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["customer_name"] . "</td>";
                            echo "<td>" . $row["room_id"] . "</td>";
                            echo "<td><span class='status-badge " . $row["status"] . "'>" . ucfirst($row["status"]) . "</span></td>";
                            
                            if (in_array($_SESSION["role"], ["admin", "staff"])) {
                                echo "<td>
                                    <form method='POST'>
                                        <input type='hidden' name='booking_id' value='" . $row["id"] . "'>
                                        <select name='status'>
                                            <option value='pending' " . ($row['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                                            <option value='confirmed' " . ($row['status'] == 'confirmed' ? 'selected' : '') . ">Confirmed</option>
                                            <option value='cancelled' " . ($row['status'] == 'cancelled' ? 'selected' : '') . ">Cancelled</option>
                                        </select>
                                        <input type='submit' name='update' value='Update'>
                                    </form>
                                </td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='" . (in_array($_SESSION["role"], ["admin", "staff"]) ? "5" : "4") . "'>No bookings found</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <div style="text-align: right; margin-top: 1rem;">
                    <?php if (in_array($_SESSION["role"], ["admin", "staff"])) { ?>
                        <a href="manage_bookings.php" class="btn-primary" style="text-decoration: none; padding: 0.5rem 1rem;">View All Bookings &rarr;</a>
                    <?php } ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
