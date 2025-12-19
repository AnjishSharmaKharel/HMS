<?php
session_start();
include "config.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION["role"]) || !in_array($_SESSION["role"], ["admin", "staff"])) {
    header("Location: dashboard.php");
    exit();
}

// Add new room
if (isset($_POST["add_room"])) {
    $type = $_POST["room_type"];
    $price = $_POST["price"];
    $desc = $_POST["description"];
    $status = $_POST["status"];

    $sql = "INSERT INTO rooms (room_type, price, description, status) VALUES ('$type', '$price', '$desc', '$status')";
    $conn->query($sql);
}

// Delete room
if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $conn->query("DELETE FROM rooms WHERE id=$id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms</title>
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
        .card { background-color: #fff; padding: 2rem; border-radius: .5rem; box-shadow: 0 4px 6px rgba(0,0,0,.1); margin-bottom: 2rem; border: 1px solid var(--border, #d0d0d0); }
        .card h3 { margin-bottom: 1.5rem; color: var(--primary, #2d4a8c); }
        table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; background-color: #fff; box-shadow: 0 4px 6px rgba(0,0,0,.1); border-radius: .5rem; overflow: hidden; }
        table th, table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border, #d0d0d0); }
        table th { background-color: var(--primary, #2d4a8c); color: #fff; font-weight: 600; text-transform: uppercase; font-size: .875rem; letter-spacing: .05em; }
        table tr:last-child td { border-bottom: none; }
        table tr:nth-child(even) { background-color: var(--muted-light, #f5f5f5); }
        table tr:hover { background-color: rgba(201, 184, 136, .1); }
        table a[href*="delete"] { color: var(--error, #ef4444); text-decoration: none; font-weight: 500; padding: .25rem .5rem; border-radius: .25rem; transition: background-color .2s; }
        table a[href*="delete"]:hover { background-color: rgba(239, 68, 68, 0.1); }
        main form { background-color: #fff; padding: 2rem; border-radius: .5rem; box-shadow: 0 4px 6px rgba(0,0,0,.1); border: 1px solid var(--border, #d0d0d0); max-width: 600px; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: .5rem; font-weight: 500; color: var(--foreground, #1a1a1a); font-size: .95rem; }
        main form input[type="text"], main form input[type="number"], main form textarea, main form select { width: 100%; padding: .75rem; border: 1px solid var(--border, #d0d0d0); border-radius: .375rem; font-size: 1rem; transition: border-color .2s; }
        main form input[type="text"]:focus, main form input[type="number"]:focus, main form textarea:focus, main form select:focus { outline: none; border-color: var(--primary, #2d4a8c); box-shadow: 0 0 0 3px rgba(45, 74, 140, 0.1); }
        main form textarea { min-height: 100px; resize: vertical; }
        main form input[type="submit"] { background-color: var(--primary, #2d4a8c); color: #fff; padding: .75rem 2rem; border: none; border-radius: .375rem; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background-color .2s; width: 100%; }
        main form input[type="submit"]:hover { background-color: var(--primary-dark, #1f3264); }
        .status-badge { padding: .25rem .75rem; border-radius: 9999px; font-size: .75rem; font-weight: 600; text-transform: uppercase; display: inline-block; }
        .status-badge.available { background-color: rgba(16,185,129,.1); color: var(--success, #10b981); }
        .status-badge.booked { background-color: rgba(239,68,68,.1); color: var(--error, #ef4444); }
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
                    <li><a href="dashboard.php">📊 Dashboard</a></li>
                    <li><a href="manage_rooms.php" class="active">🛏️ Manage Rooms</a></li>
                    <li><a href="manage_bookings.php">📅 Manage Bookings</a></li>
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
                <h2>Manage Rooms</h2>
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
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Add New Room</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label for="room_type">Room Type</label>
                            <input type="text" id="room_type" name="room_type" placeholder="e.g. Deluxe Suite" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Price (Rs)</label>
                            <input type="number" step="0.01" id="price" name="price" placeholder="0.00" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" placeholder="Enter room description..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="available">Available</option>
                                <option value="booked">Booked</option>
                            </select>
                        </div>
                        
                        <input type="submit" name="add_room" value="Add Room">
                    </form>
                </div>

                <div class="card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">All Rooms</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Booking</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM rooms ORDER BY id DESC");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["room_type"] . "</td>";
                            echo "<td>$" . $row["price"] . "</td>";
                            echo "<td>" . $row["description"] . "</td>";
                            $roomId = intval($row["id"]);
                            $bookingRes = $conn->query("SELECT customer_name, status FROM bookings WHERE room_id=$roomId AND status!='cancelled' ORDER BY id DESC LIMIT 1");
                            if ($bookingRes && $bookingRes->num_rows > 0) {
                                $bk = $bookingRes->fetch_assoc();
                                echo "<td>Booked by " . htmlspecialchars($bk["customer_name"]) . " (" . htmlspecialchars($bk["status"]) . ")</td>";
                            } else {
                                echo "<td>—</td>";
                            }
                            echo "<td><span class='status-badge " . $row["status"] . "'>" . ucfirst($row["status"]) . "</span></td>";
                            echo "<td><a href='manage_rooms.php?delete=" . $row["id"] . "' onclick=\"return confirm('Are you sure you want to delete this room?');\">Delete</a></td>";
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
