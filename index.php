<?php
session_start();
include "config.php";
$baseUrl = $_SERVER["REQUEST_URI"];
$currentPage = basename($baseUrl, ".php") ?: "home";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxeHotel - Premium Hotel Management</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="navbar-container">
            <a href="index.php" class="navbar-logo">
                <div class="navbar-logo-icon">LH</div>
                <span class="navbar-logo-text">LuxeHotel</span>
            </a>

            <div class="navbar-links">
                <a href="#home">Home</a>
                <a href="#rooms">Rooms</a>
                <a href="#services">Services</a>
                <a href="#contact">Contact</a>
            </div>

            <div class="navbar-cta">
                <?php if (isset($_SESSION["username"])) { ?>
                    <strong>Hello <?php echo $_SESSION["username"]; ?></strong>
                    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") { ?>
                        <a href="manage_staff.php" class="login-btn">Manage Staff</a>
                    <?php } elseif (isset($_SESSION["role"]) && $_SESSION["role"] === "customer") { ?>
                        <a href="bookings.php" class="login-btn">My Bookings</a>
                    <?php } ?>
                    <a href="logout.php" class="login-btn">Logout</a>

                <?php } else { ?>
                <a href="login.php" class="login-btn">Login</a>
                <?php } ?>
                <button class="menu-toggle" onclick="toggleMenu()">☰</button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu-items">
                <a href="#home">Home</a>
                <a href="#rooms">Rooms</a>
                <a href="#services">Services</a>
                <a href="#contact">Contact</a>
                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") { ?>
                    <a href="manage_staff.php">Manage Staff</a>
                    <a href="logout.php">Logout</a>
                <?php } elseif (isset($_SESSION["role"]) && $_SESSION["role"] === "customer") { ?>
                    <a href="bookings.php">My Bookings</a>
                    <a href="logout.php">Logout</a>
                <?php } else { ?>
                    <a href="login.php">Login</a>
                <?php } ?>
            </div>
        </div>
    </nav>

    <!-- Body Section -->
    <section class="body" id="home">
        <div class="body-container">
            <div class="body-content">
                <h1>Welcome to LuxeHotel</h1>
                <p>Experience unparalleled luxury and comfort in the heart of the city. Your perfect getaway awaits with world-class service and exceptional amenities.</p>
                <div class="body-buttons">
                    <a href="#rooms" class="btn btn-primary">Explore Rooms</a>
                    <a href="login.php" class="btn btn-outline">Staff Login</a>
                </div>
            </div>
            <div class="body-icon">
                <span class="body-icon-emoji">🏨</span>
                <p class="body-icon-text">5-Star Premium Hotel Experience</p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Happy Guests</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">25</div>
                <div class="stat-label">Years of Excellence</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">150</div>
                <div class="stat-label">Premium Rooms</div>
            </div>
        </div>
    </section>

    <!-- Featured Rooms Section -->
    <section class="rooms-section" id="rooms">
        <div class="section-header">
            <h2>Our Featured Rooms</h2>
            <p>Choose from our selection of beautifully designed rooms, each offering comfort and elegance for your stay.</p>
        </div>

        <div class="rooms-container">
            <div class="rooms-grid">
                <?php
                // Group rooms by type to show availability
                $rooms = $conn->query("SELECT 
                    room_type, 
                    MAX(price) as price, 
                    MAX(description) as description, 
                    COUNT(*) as total_rooms,
                    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_rooms 
                    FROM rooms 
                    GROUP BY room_type 
                    ORDER BY available_rooms DESC");

                if ($rooms && $rooms->num_rows > 0) {
                    while ($room = $rooms->fetch_assoc()) {
                        $icon = "🛏️";
                        if (stripos($room["room_type"], "Deluxe") !== false) { $icon = "👑"; }
                        else if (stripos($room["room_type"], "Suite") !== false) { $icon = "🏰"; }
                        
                        echo "<div class='room-card'>";
                        echo "<div class='room-image'>" . htmlspecialchars($icon) . "</div>";
                        echo "<div class='room-content'>";
                        echo "<h3 class='room-name'>" . htmlspecialchars($room["room_type"]) . "</h3>";
                        echo "<p class='room-description'>" . htmlspecialchars($room["description"]) . "</p>";
                        
                        // Availability Status
                        $available = intval($room["available_rooms"]);
                        $total = intval($room["total_rooms"]);
                        $statusColor = $available > 0 ? "#10b981" : "#ef4444";
                        
                        echo "<div style='margin: 1rem 0; font-size: 0.9rem;'>";
                        echo "<span style='color: " . $statusColor . "; font-weight: bold;'>● " . ($available > 0 ? "Available" : "Fully Booked") . "</span>";
                        echo "<span style='color: #6b7280; margin-left: 0.5rem;'>(" . $available . " of " . $total . " rooms free)</span>";
                        echo "</div>";

                        echo "<div class='room-footer'>";
                        echo "<div class='room-price'>";
                        echo "<div class='room-price-amount'>Rs." . htmlspecialchars($room["price"]) . "</div>";
                        echo "<div class='room-price-label'>per night</div>";
                        echo "</div>";
                        
                        if ($available > 0) {
                            echo "<a href='book_room.php?type=" . urlencode($room["room_type"]) . "' class='btn-book'>Book Now</a>";
                        } else {
                            echo "<span class='btn-book' style='pointer-events:none;opacity:.6;background:#9ca3af;'>Sold Out</span>";
                        }
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No rooms available right now.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section" id="services">
        <div class="section-header">
            <h2>Premium Services</h2>
            <p>Enjoy world-class facilities and services designed for your comfort and convenience.</p>
        </div>

        <div class="services-container">
            <div class="services-grid">
                <div class="service-item">
                    <div class="service-icon">📶</div>
                    <h3 class="service-name">High-Speed WiFi</h3>
                    <p class="service-description">Complimentary WiFi in all rooms and public areas</p>
                </div>

                <div class="service-item">
                    <div class="service-icon">🍽️</div>
                    <h3 class="service-name">Restaurant & Bar</h3>
                    <p class="service-description">Fine dining experience with international cuisine</p>
                </div>

                <div class="service-item">
                    <div class="service-icon">💪</div>
                    <h3 class="service-name">Fitness Center</h3>
                    <p class="service-description">Fully equipped gym with personal trainers</p>
                </div>

                <div class="service-item">
                    <div class="service-icon">🏊</div>
                    <h3 class="service-name">Swimming Pool</h3>
                    <p class="service-description">Olympic-size pool with heated water</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
        <div class="contact-container">
            <h2>Ready to Book Your Stay?</h2>
            <p>Contact us today or use our online booking system to reserve your room.</p>

            <div class="contact-buttons">
                <a href="tel:056-524550" class="btn-contact btn-contact-primary">📞    056-524550</a>
                <a href="mailto:info@luxehotel.com" class="btn-contact btn-contact-outline">✉️ Email Us</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h4>LuxeHotel</h4>
                    <p>Experience luxury and comfort at our premier hotel. Your perfect getaway awaits.</p>
                </div>

                <div class="footer-section">
                    <h4>Contact</h4>
                    <div class="footer-section-contact">
                        <span>📍</span>
                        <p>Bharatpur, Chitwan, Nepal</p>
                    </div>
                    <div class="footer-section-contact">
                        <span>📞</span>
                        <p>056-524550</p>
                    </div>
                    <div class="footer-section-contact">
                        <span>📧</span>
                        <p>info@luxehotel.com</p>
                    </div>
                </div>

              

                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <a href="#rooms">Rooms</a>
                    <a href="#services">Services</a>
                    <a href="login.php">Staff Login</a>
                </div>
            </div>

            <div class="footer-divider">
                <p>© 2025 LuxeHotel. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="main.js"></script>
</body>
</html>
