<?php
session_start();
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
                <a href="login.php">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Welcome to LuxeHotel</h1>
                <p>Experience unparalleled luxury and comfort in the heart of the city. Your perfect getaway awaits with world-class service and exceptional amenities.</p>
                <div class="hero-buttons">
                    <a href="#rooms" class="btn btn-primary">Explore Rooms</a>
                    <a href="login.php" class="btn btn-outline">Staff Login</a>
                </div>
            </div>
            <div class="hero-icon">
                <span class="hero-icon-emoji">🏨</span>
                <p class="hero-icon-text">5-Star Premium Hotel Experience</p>
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
                <!-- Standard Room -->
                <div class="room-card">
                    <div class="room-image">🛏️</div>
                    <div class="room-content">
                        <h3 class="room-name">Standard Room</h3>
                        <p class="room-description">Cozy room with essential amenities</p>

                        <div class="room-features">
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>Queen Bed</span>
                            </div>
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>Private Bath</span>
                            </div>
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>WiFi</span>
                            </div>
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>AC</span>
                            </div>
                        </div>

                        <div class="room-footer">
                            <div class="room-price">
                                <div class="room-price-amount">Rs.1500</div>
                                <div class="room-price-label">per night</div>
                            </div>
                            <button class="btn-book">Book Now</button>
                        </div>
                    </div>
                </div>

                <!-- Deluxe Room -->
                <div class="room-card">
                    <div class="room-image">👑</div>
                    <div class="room-content">
                        <h3 class="room-name">Deluxe Room</h3>
                        <p class="room-description">Spacious room with premium furnishings</p>

                        <div class="room-features">
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>King Bed</span>
                            </div>
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>Luxury Bath</span>
                            </div>
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>WiFi</span>
                            </div>
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>Mini Bar</span>
                            </div>
                        </div>

                        <div class="room-footer">
                            <div class="room-price">
                                <div class="room-price-amount">Rs.2500</div>
                                <div class="room-price-label">per night</div>
                            </div>
                            <button class="btn-book">Book Now</button>
                        </div>
                    </div>
                </div>

                <!-- Suite -->
                <div class="room-card">
                    <div class="room-image">🏰</div>
                    <div class="room-content">
                        <h3 class="room-name">Suite</h3>
                        <p class="room-description">Ultimate luxury with separate living area</p>

                        <div class="room-features">
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>King Bed</span>
                            </div>
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>Jacuzzi</span>
                            </div>
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>Lounge</span>
                            </div>
                            <div class="room-feature">
                                <span class="room-feature-icon">⭐</span>
                                <span>Premium Service</span>
                            </div>
                        </div>

                        <div class="room-footer">
                            <div class="room-price">
                                <div class="room-price-amount">Rs.3500</div>
                                <div class="room-price-label">per night</div>
                            </div>
                            <button class="btn-book">Book Now</button>
                        </div>
                    </div>
                </div>
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

    <script>
        // Mobile Menu Toggle
        function toggleMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('active');
        }

        // Close mobile menu when clicking on a link
        const mobileMenuLinks = document.querySelectorAll('.mobile-menu-items a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobileMenu').classList.remove('active');
            });
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
