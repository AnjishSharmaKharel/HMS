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

if (isset($_POST["update_room"])) {
    $update_errors = [];
    $room_id = intval($_POST["room_id"]);
    $type_raw = isset($_POST["room_type"]) ? trim($_POST["room_type"]) : "";
    $price = $_POST["price"];
    $desc = $_POST["description"];
    $status = $_POST["status"];
    $copies = isset($_POST["create_copies"]) ? intval($_POST["create_copies"]) : 0;

    if ($type_raw === "" || !preg_match("/^[A-Za-z][A-Za-z\s\-]{2,}$/", $type_raw)) {
        $update_errors[] = "Room type must be text (letters, spaces, dashes) and at least 3 characters.";
    }
    if (empty($update_errors)) {
        $type = $conn->real_escape_string($type_raw);
        $price = $conn->real_escape_string($price);
        $desc = $conn->real_escape_string($desc);
        $status = $conn->real_escape_string($status);
        $conn->query("UPDATE rooms SET room_type='$type', price='$price', description='$desc', status='$status' WHERE id=$room_id");
        
        if ($copies > 0) {
            $sql_insert = "INSERT INTO rooms (room_type, price, description, status) VALUES ('$type', '$price', '$desc', '$status')";
            for ($i = 0; $i < $copies; $i++) {
                $conn->query($sql_insert);
            }
            $update_success = "Room updated and $copies copies created successfully.";
        } else {
            $update_success = "Room updated successfully.";
        }
    }
}

if (isset($_POST["add_room"])) {
    $form_errors = [];
    $type_raw = isset($_POST["room_type"]) ? trim($_POST["room_type"]) : "";
    $price = $_POST["price"];
    $desc = $_POST["description"];
    $status = $_POST["status"];
    $quantity = isset($_POST["quantity"]) ? intval($_POST["quantity"]) : 1;
    
    if ($type_raw === "" || !preg_match("/^[A-Za-z][A-Za-z\s\-]{2,}$/", $type_raw)) {
        $form_errors[] = "Room type must be text (letters, spaces, dashes) and at least 3 characters.";
    }
    if ($quantity < 1) {
        $quantity = 1;
    }
    
    if (empty($form_errors)) {
        $type = $conn->real_escape_string($type_raw);
        $price = $conn->real_escape_string($price);
        $desc = $conn->real_escape_string($desc);
        $status = $conn->real_escape_string($status);
        
        $sql = "INSERT INTO rooms (room_type, price, description, status) VALUES ('$type', '$price', '$desc', '$status')";
        
        $success_count = 0;
        for ($i = 0; $i < $quantity; $i++) {
            if ($conn->query($sql)) {
                $success_count++;
            }
        }
        
        if ($success_count > 0) {
            // Optional: Set a success message if you were using one
        }
    }
}

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
                <?php
                    $edit_room = null;
                    if (isset($_GET["edit"])) {
                        $edit_id = intval($_GET["edit"]);
                        $res = $conn->query("SELECT * FROM rooms WHERE id=$edit_id LIMIT 1");
                        if ($res && $res->num_rows === 1) {
                            $edit_room = $res->fetch_assoc();
                        }
                    }
                ?>
                <?php if ($edit_room) { ?>
                <div class="card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Edit Room</h3>
                    <?php
                        if (isset($update_errors) && !empty($update_errors)) {
                            echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #ef4444;color:#ef4444;background:rgba(239,68,68,.08);border-radius:.25rem;'>" . implode("<br>", array_map('htmlspecialchars', $update_errors)) . "</div>";
                        }
                        if (isset($update_success) && $update_success !== "") {
                            echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #10b981;color:#10b981;background:rgba(16,185,129,.08);border-radius:.25rem;'>" . htmlspecialchars($update_success) . "</div>";
                        }
                    ?>
                    <form method="POST">
                        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($edit_room["id"]); ?>">
                        <div class="form-group">
                            <label for="edit_room_type">Room Type</label>
                            <input type="text" id="edit_room_type" name="room_type" value="<?php echo htmlspecialchars($edit_room["room_type"]); ?>" required pattern="^[A-Za-z][A-Za-z\s\-]{2,}$" title="Enter letters, spaces, and dashes only">
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_price">Price (Rs)</label>
                            <input type="number" step="0.01" id="edit_price" name="price" value="<?php echo htmlspecialchars($edit_room["price"]); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_description">Description</label>
                            <textarea id="edit_description" name="description" required><?php echo htmlspecialchars($edit_room["description"]); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_status">Status</label>
                            <select id="edit_status" name="status">
                                <option value="available" <?php echo ($edit_room["status"] === "available" ? "selected" : ""); ?>>Available</option>
                                <option value="booked" <?php echo ($edit_room["status"] === "booked" ? "selected" : ""); ?>>Booked</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="create_copies">Create Copies (Optional)</label>
                            <input type="number" id="create_copies" name="create_copies" min="0" value="0">
                            <small style="color: #6b7280;">Enter a number to create new rooms with these same details.</small>
                        </div>
                        
                        <input type="submit" name="update_room" value="Update Room">
                    </form>
                </div>
                <?php } ?>
                <div class="card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Add New Room</h3>
                    <?php
                        if (isset($form_errors) && !empty($form_errors)) {
                            echo "<div style='margin-bottom:1rem;padding:.75rem 1rem;border:1px solid #ef4444;color:#ef4444;background:rgba(239,68,68,.08);border-radius:.25rem;'>" . implode("<br>", array_map('htmlspecialchars', $form_errors)) . "</div>";
                        }
                    ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="room_type">Room Type</label>
                            <input type="text" id="room_type" name="room_type" placeholder="e.g. Deluxe Suite" required pattern="^[A-Za-z][A-Za-z\s\-]{2,}$" title="Enter letters, spaces, and dashes only">
                        </div>
                        
                        <div class="form-group">
                            <label for="quantity">Number of Rooms</label>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" required>
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
                            echo "<td>Rs" . $row["price"] . "</td>";
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
                            echo "<td><a href='manage_rooms.php?edit=" . $row["id"] . "'>Edit</a> | <a href='manage_rooms.php?delete=" . $row["id"] . "' onclick=\"return confirm('Are you sure you want to delete this room?');\">Delete</a></td>";
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
