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

// Fetch Stats
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status='pending'")->fetch_assoc()['count'];
$total_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms")->fetch_assoc()['count'];
$available_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms WHERE status='available'")->fetch_assoc()['count'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body.admin-body { background-color: #f3f4f6; }
        .admin-container { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 280px; background-color: #111827; color: #fff; flex-shrink: 0; display: flex; flex-direction: column; }
        .sidebar-header { height: 4rem; display: flex; align-items: center; padding: 0 1.5rem; border-bottom: 1px solid #1f2937; }
        .sidebar-brand { font-size: 1.25rem; font-weight: 700; color: #fff; text-decoration: none; display: flex; align-items: center; gap: .75rem; }
        .sidebar-nav { flex: 1; padding: 1.5rem 1rem; }
        .sidebar-nav ul { list-style: none; display: flex; flex-direction: column; gap: .5rem; margin: 0; padding: 0; }
        .sidebar-nav a { display: flex; align-items: center; gap: .75rem; padding: .75rem 1rem; color: #9ca3af; text-decoration: none; border-radius: .5rem; font-weight: 500; transition: all .2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: #1f2937; color: #fff; }
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid #1f2937; }
        .admin-main { flex: 1; display: flex; flex-direction: column; overflow-x: hidden; }
        .admin-topbar { height: 4rem; background-color: #fff; border-bottom: 1px solid var(--border, #d0d0d0); display: flex; justify-content: space-between; align-items: center; padding: 0 2rem; }
        .admin-content { flex: 1; padding: 2rem; max-width: 1400px; width: 100%; margin: 0 auto; }
        .user-profile { display: flex; align-items: center; gap: .75rem; }
        .user-avatar { width: 2.5rem; height: 2.5rem; background-color: var(--primary-light, #416eb4); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .user-name { font-weight: 600; font-size: .875rem; color: var(--foreground, #1a1a1a); }
        .user-role { font-size: .75rem; color: #6b7280; text-transform: capitalize; }
        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background-color: #fff; padding: 1.5rem; border-radius: .5rem; box-shadow: 0 4px 6px rgba(0,0,0,.1); border-left: 4px solid var(--primary, #2d4a8c); display: flex; flex-direction: column; }
        .stat-card-title { font-size: .875rem; color: var(--foreground, #1a1a1a); opacity: .7; margin-bottom: .5rem; text-transform: uppercase; font-weight: 600; }
        .stat-card-value { font-size: 2rem; font-weight: 700; color: var(--primary, #2d4a8c); }
        .stat-card.pending { border-left-color: var(--warning, #f59e0b); }
        .stat-card.pending .stat-card-value { color: var(--warning, #f59e0b); }
        .stat-card.success { border-left-color: var(--success, #10b981); }
        .stat-card.success .stat-card-value { color: var(--success, #10b981); }
        .dashboard-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .action-card { background-color: #fff; padding: 1.5rem; border-radius: .5rem; box-shadow: 0 2px 4px rgba(0,0,0,.05); border: 1px solid var(--border, #d0d0d0); text-decoration: none; color: var(--foreground, #1a1a1a); transition: transform .2s, box-shadow .2s, border-color .2s; display: flex; align-items: center; gap: 1rem; }
        .action-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,.1); border-color: var(--primary, #2d4a8c); }
        .action-icon { font-size: 2rem; color: var(--primary, #2d4a8c); }
        .action-info h3 { font-size: 1.125rem; margin-bottom: .25rem; color: var(--primary, #2d4a8c); }
        .action-info p { font-size: .875rem; color: var(--foreground, #1a1a1a); opacity: .8; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; background-color: #fff; box-shadow: 0 4px 6px rgba(0,0,0,.1); border-radius: .5rem; overflow: hidden; }
        table th, table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border, #d0d0d0); }
        table th { background-color: var(--primary, #2d4a8c); color: #fff; font-weight: 600; text-transform: uppercase; font-size: .875rem; letter-spacing: .05em; }
        table tr:last-child td { border-bottom: none; }
        table tr:nth-child(even) { background-color: var(--muted-light, #f5f5f5); }
        table tr:hover { background-color: rgba(201, 184, 136, .1); }
        table form { display: flex; gap: .5rem; align-items: center; }
        table select { padding: .375rem .75rem; border: 1px solid var(--border, #d0d0d0); border-radius: .25rem; background-color: #fff; font-size: .875rem; cursor: pointer; outline: none; transition: border-color .2s; }
        table select:focus { border-color: var(--primary, #2d4a8c); }
        table input[type="submit"] { padding: .375rem .75rem; background-color: var(--success, #10b981); color: #fff; border: none; border-radius: .25rem; font-size: .875rem; font-weight: 500; cursor: pointer; transition: background-color .2s; }
        table input[type="submit"]:hover { background-color: #059669; }
        .status-badge { padding: .25rem .75rem; border-radius: 9999px; font-size: .75rem; font-weight: 600; text-transform: uppercase; display: inline-block; }
        .status-badge.pending { background-color: rgba(245,158,11,.1); color: var(--warning, #f59e0b); }
        .status-badge.confirmed, .status-badge.available { background-color: rgba(16,185,129,.1); color: var(--success, #10b981); }
        .status-badge.cancelled, .status-badge.booked { background-color: rgba(239,68,68,.1); color: var(--error, #ef4444); }
    </style>
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
                    <li><a href="manage_rooms.php">🛏️ Manage Rooms</a></li>
                    <li><a href="manage_bookings.php">📅 Manage Bookings</a></li>
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
                <h2>Dashboard Overview</h2>
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
                </div>

                <h2>Quick Actions</h2>
                <div class="dashboard-actions">
                    <a href="manage_rooms.php" class="action-card">
                        <div class="action-icon">🛏️</div>
                        <div class="action-info">
                            <h3>Manage Rooms</h3>
                            <p>Add, edit, or delete rooms</p>
                        </div>
                    </a>
                    <a href="manage_bookings.php" class="action-card">
                        <div class="action-icon">📅</div>
                        <div class="action-info">
                            <h3>Manage Bookings</h3>
                            <p>View and update booking status</p>
                        </div>
                    </a>
                    <a href="manage_staff.php" class="action-card">
                        <div class="action-icon">👥</div>
                        <div class="action-info">
                            <h3>Manage Staff</h3>
                            <p>Add or remove staff members</p>
                        </div>
                    </a>
                </div>

                <h2>Recent Bookings</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Room ID</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM bookings ORDER BY id DESC LIMIT 5");
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["customer_name"] . "</td>";
                            echo "<td>" . $row["room_id"] . "</td>";
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
                    } else {
                        echo "<tr><td colspan='5'>No bookings found</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <div style="text-align: right; margin-top: 1rem;">
                    <a href="manage_bookings.php" class="btn-primary" style="text-decoration: none; padding: 0.5rem 1rem;">View All Bookings &rarr;</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
