<?php
session_start();
include "config.php";

$is_customer = isset($_SESSION["username"]) && isset($_SESSION["role"]) && $_SESSION["role"] === "customer";
$customer_email_session = $is_customer ? $_SESSION["username"] : null;

$type_raw = isset($_REQUEST["type"]) ? trim($_REQUEST["type"]) : "";
if ($type_raw === "") {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Booking Error</title><link rel='stylesheet' href='style.css'></head><body><div class='admin-content'><div class='card'><h3>Invalid request</h3><p>Room type is required.</p><a href='index.php' class='btn-primary' style='text-decoration:none;padding:.5rem 1rem;'>Back to Home</a></div></div></body></html>";
    exit();
}

$type = $conn->real_escape_string($type_raw);

// Enforce login
if (!$is_customer) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Login Required</title><link rel='stylesheet' href='style.css'></head><body><div class='admin-content'><div class='card'><h3>Login Required</h3><p>Please sign in or register as a customer to book rooms.</p><div style='display:flex;gap:.5rem;'><a href='login.php?type=customer' class='btn-primary' style='text-decoration:none;padding:.5rem 1rem;'>Customer Login</a><a href='register.php' class='btn-primary' style='text-decoration:none;padding:.5rem 1rem;'>Register</a></div></div></div></body></html>";
    exit();
}

// Get Customer Details
$custRes = $conn->query("SELECT customer_fullname, customer_email FROM customer WHERE customer_email='" . $conn->real_escape_string($customer_email_session) . "' LIMIT 1");
if (!$custRes || $custRes->num_rows !== 1) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Account Not Found</title><link rel='stylesheet' href='style.css'></head><body><div class='admin-content'><div class='card'><h3>Account Not Found</h3><p>Your customer account could not be found. Please register.</p><a href='register.php' class='btn-primary' style='text-decoration:none;padding:.5rem 1rem;'>Register</a></div></div></body></html>";
    exit();
}
$cust = $custRes->fetch_assoc();
$customer_name = $cust["customer_fullname"];
$customer_email = $cust["customer_email"];

// Check Availability
$availRes = $conn->query("SELECT COUNT(*) as count, MAX(price) as price FROM rooms WHERE room_type='$type' AND status='available'");
$availData = $availRes->fetch_assoc();
$available_count = intval($availData["count"]);
$price_per_night = floatval($availData["price"]);

if ($available_count === 0) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Room Unavailable</title><link rel='stylesheet' href='style.css'></head><body><div class='admin-content'><div class='card'><h3>Room Unavailable</h3><p>No available '$type' rooms at the moment.</p><a href='index.php#rooms' class='btn-primary' style='text-decoration:none;padding:.5rem 1rem;'>Browse Rooms</a></div></div></body></html>";
    exit();
}

// Handle Booking Submission
if (isset($_POST["confirm_booking"])) {
    $quantity = isset($_POST["quantity"]) ? intval($_POST["quantity"]) : 1;
    $check_in = isset($_POST["check_in"]) ? $_POST["check_in"] : date("Y-m-d");
    $check_out = isset($_POST["check_out"]) ? $_POST["check_out"] : date("Y-m-d", strtotime("+1 day"));
    
    if ($quantity < 1) $quantity = 1;
    if ($quantity > $available_count) {
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Booking Error</title><link rel='stylesheet' href='style.css'></head><body><div class='admin-content'><div class='card'><h3>Not Enough Rooms</h3><p>Only $available_count rooms are available.</p><a href='javascript:history.back()' class='btn-primary' style='text-decoration:none;padding:.5rem 1rem;'>Go Back</a></div></div></body></html>";
        exit();
    }
    
    // Lock and Book Rooms
    // Ideally use transaction, but for this simple setup:
    $roomsToBookRes = $conn->query("SELECT id FROM rooms WHERE room_type='$type' AND status='available' LIMIT $quantity");
    
    if ($roomsToBookRes->num_rows < $quantity) {
         echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Booking Error</title><link rel='stylesheet' href='style.css'></head><body><div class='admin-content'><div class='card'><h3>Availability Changed</h3><p>Some rooms were just booked by another user. Please try again.</p><a href='book_room.php?type=" . urlencode($type) . "' class='btn-primary' style='text-decoration:none;padding:.5rem 1rem;'>Try Again</a></div></div></body></html>";
         exit();
    }
    
    $booked_ids = [];
    while ($roomRow = $roomsToBookRes->fetch_assoc()) {
        $r_id = $roomRow["id"];
        $ins = $conn->query("INSERT INTO bookings (customer_name, customer_email, room_id, check_in, check_out, status) VALUES ('" . $conn->real_escape_string($customer_name) . "', '" . $conn->real_escape_string($customer_email) . "', $r_id, '$check_in', '$check_out', 'pending')");
        if ($ins) {
            $conn->query("UPDATE rooms SET status='booked' WHERE id=$r_id");
            $booked_ids[] = $r_id;
        }
    }
    
    if (count($booked_ids) > 0) {
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Booking Confirmed</title><link rel='stylesheet' href='style.css'></head><body><div class='admin-content'><div class='card'><h3>Booking Confirmed!</h3><p>Successfully booked " . count($booked_ids) . " " . htmlspecialchars($type) . " room(s).</p><p><strong>Check-in:</strong> $check_in<br><strong>Check-out:</strong> $check_out</p><div style='display:flex;gap:.5rem;'><a href='index.php' class='btn-primary' style='text-decoration:none;padding:.5rem 1rem;'>Back to Home</a><a href='manage_bookings.php' class='btn-primary' style='text-decoration:none;padding:.5rem 1rem;'>View Bookings</a></div></div></div></body></html>";
    } else {
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Booking Failed</title><link rel='stylesheet' href='style.css'></head><body><div class='admin-content'><div class='card'><h3>Booking Failed</h3><p>System error occurred.</p><a href='index.php' class='btn-primary' style='text-decoration:none;padding:.5rem 1rem;'>Back to Home</a></div></div></body></html>";
    }
    exit();
}

// Display Booking Form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room - LuxeHotel</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .booking-container { max-width: 600px; margin: 4rem auto; padding: 0 1rem; }
        .booking-card { background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-input { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; }
        .btn-submit { width: 100%; padding: 0.75rem; background: var(--primary); color: white; border: none; border-radius: 0.375rem; font-size: 1rem; font-weight: 600; cursor: pointer; }
        .btn-submit:hover { background: var(--primary-dark); }
        .room-summary { background: #f3f4f6; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <nav>
        <div class="navbar-container">
            <a href="index.php" class="navbar-logo">
                <div class="navbar-logo-icon">LH</div>
                <span class="navbar-logo-text">LuxeHotel</span>
            </a>
            <div class="navbar-links">
                <a href="index.php">Home</a>
                <a href="index.php#rooms">Rooms</a>
            </div>
        </div>
    </nav>

    <div class="booking-container">
        <div class="booking-card">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary);">Complete Your Booking</h2>
            
            <div class="room-summary">
                <h3 style="margin-top: 0;"><?php echo htmlspecialchars($type); ?></h3>
                <p style="margin-bottom: 0;">Price: Rs.<?php echo number_format($price_per_night, 2); ?> / night</p>
                <p style="margin-top: 0.5rem; color: #10b981;">Only <?php echo $available_count; ?> rooms available!</p>
            </div>

            <form method="POST">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                
                <div class="form-group">
                    <label>Number of Rooms</label>
                    <select name="quantity" class="form-input" required>
                        <?php 
                        for($i=1; $i<=$available_count; $i++) {
                            echo "<option value='$i'>$i Room" . ($i>1 ? 's' : '') . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label>Check-in Date</label>
                        <input type="date" name="check_in" class="form-input" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div>
                        <label>Check-out Date</label>
                        <input type="date" name="check_out" class="form-input" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Guest Details</label>
                    <input type="text" value="<?php echo htmlspecialchars($customer_name); ?>" class="form-input" readonly style="background: #f9fafb;">
                    <small style="color: #6b7280;">Logged in as <?php echo htmlspecialchars($customer_email); ?></small>
                </div>

                <button type="submit" name="confirm_booking" class="btn-submit">Confirm Booking</button>
            </form>
        </div>
    </div>
</body>
</html>
