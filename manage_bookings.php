<?php
session_start();
include "config.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Update booking status
if (isset($_POST["update"])) {
    $id = $_POST["booking_id"];
    $status = $_POST["status"];
    $conn->query("UPDATE bookings SET status='$status' WHERE id=$id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
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
                    <li><a href="manage_bookings.php" class="active">📅 Manage Bookings</a></li>
                    <li><a href="manage_staff.php">👥 Manage Staff</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php">🚪 Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-topbar">
                <h2>Manage Bookings</h2>
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
                <div class="card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">All Bookings</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Room ID</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM bookings ORDER BY id DESC");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["customer_name"] . "</td>";
                            echo "<td>" . $row["customer_email"] . "</td>";
                            echo "<td>" . $row["room_id"] . "</td>";
                            echo "<td>" . $row["check_in"] . "</td>";
                            echo "<td>" . $row["check_out"] . "</td>";
                            echo "<td><span class='status-badge " . $row["status"] . "'>" . ucfirst($row["status"]) . "</span></td>";
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
                            echo "</tr>";
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
