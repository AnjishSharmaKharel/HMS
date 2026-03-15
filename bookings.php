<?php
session_start();
include "config.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php?type=customer");
    exit();
}

$customer_email = $_SESSION["username"];

if (isset($_GET["cancel"])) {
    $booking_id = intval($_GET["cancel"]);
    $conn->query("UPDATE bookings SET status='cancelled' WHERE id=$booking_id AND customer_email='$customer_email'");
    
    $room_update = $conn->query("SELECT room_id FROM bookings WHERE id=$booking_id");
    if ($room_update && $room_update->num_rows > 0) {
        $room = $room_update->fetch_assoc();
        $conn->query("UPDATE rooms SET status='available' WHERE id=" . $room['room_id']);
    }
    
    header("Location: my_bookings.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - LuxeHotel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="sidebar-brand">
                    <span style="font-size: 1.5rem;">🏨</span> LuxeHotel
                </a>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php">🏠 Home</a></li>
                    <li><a href="bookings.php" class="active">📅 My Bookings</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php">🚪 Logout</a>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <h2>My Bookings</h2>
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
                $result = $conn->query("SELECT b.*, r.room_type, r.price 
                    FROM bookings b 
                    LEFT JOIN rooms r ON b.room_id = r.id 
                    WHERE b.customer_email = '$customer_email' 
                    ORDER BY b.id DESC");
                ?>
                
                <?php if ($result && $result->num_rows > 0): ?>
                    <div class="card">
                        <h3>Your Reservations</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Room Type</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $row["id"]; ?></td>
                                        <td><?php echo htmlspecialchars($row["room_type"] ?? "Unknown"); ?></td>
                                        <td><?php echo $row["check_in"]; ?></td>
                                        <td><?php echo $row["check_out"]; ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $row["status"]; ?>">
                                                <?php echo ucfirst($row["status"]); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($row["status"] === "pending" || $row["status"] === "confirmed"): ?>
                                                <a href="bookings.php?cancel=<?php echo $row["id"]; ?>" 
                                                   onclick="return confirm('Are you sure you want to cancel this booking?')"
                                                   style="color: #ef4444;">Cancel</a>
                                            <?php else: ?>
                                                <span style="color: #9ca3af;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <h3>No Bookings Yet</h3>
                        <p>You haven't made any room reservations yet.</p>
                        <a href="index.php#rooms" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">Browse Rooms</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
