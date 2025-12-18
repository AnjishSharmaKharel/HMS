<?php
session_start();
include "config.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
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
                            <label for="price">Price ($)</label>
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
