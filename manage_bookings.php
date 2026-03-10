<?php
session_start();
include "config.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (!in_array($_SESSION["role"], ["admin", "staff"])) {
    header("Location: dashboard.php");
    exit();
}

// Update booking status
if (isset($_POST["update"])) {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="style.css">
    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") { ?>
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
        .status-badge.confirmed { background-color: rgba(16,185,129,.1); color: var(--success, #10b981); }
        .status-badge.cancelled { background-color: rgba(239,68,68,.1); color: var(--error, #ef4444); }
    </style>
    <?php } ?>
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
                    <?php if (isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "staff"])) { ?>
                        <li><a href="manage_rooms.php">🛏️ Manage Rooms</a></li>
                    <?php } ?>
                    <li><a href="manage_bookings.php" class="active">📅 Manage Bookings</a></li>
                    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") { ?>
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
