<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoLeaf – Towards a Greener Future</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>

    <!-- navigation -->
    <nav class="navbar">
        <div class="logo">
            <!--Logo Pic -->
            <img src="assets/image/EcoLeaf_icon.png" alt="Logo">
            EcoLeaf
        </div>
        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#features">Features</a>
            <a href="#about">About Us</a>
        </div>
        <a href="access_portal.php" class="btn-nav">Login / Register</a>
    </nav>

    <!-- section -->
    <section class="hero" id="home">
        <div class="hero-text">
            <h1>Towards a Greener Future</h1>
            <p>Track your carbon footprint, join campus eco-events and earn Green Points as you contribute to
                sustainability.</p>
            <a href="access_portal.php" class="btn-primary">Join the Movement</a>
        </div>
        <div class="hero-image-wrapper">
            <!-- green Image -->
            <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=1200&q=80"
                alt="Greener Future">
        </div>
    </section>

    <!-- features Section -->
    <section class="features" id="features">
        <h2 class="section-title">Features</h2>
        <p class="section-subtitle">Discover what EcoLeaf offers</p>

        <div class="features-grid">
            <!-- card 1 -->
            <div class="feature-card">
                <div class="icon-circle">
                    <!-- SVG Icon: Leaf/Event -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2">
                        </path>
                        <path d="M18 14h-8"></path>
                        <path d="M15 18h-5"></path>
                        <path d="M10 6h8v4h-8V6Z"></path>
                    </svg>
                </div>
                <h3>Green Event</h3>
                <p>Participate in campus cleanups and workshops.</p>
            </div>

            <!-- card 2 -->
            <div class="feature-card">
                <div class="icon-circle">
                    <!-- SVG Icon: Footprint -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M4 16v-2.38C4 11.5 2.97 10.5 3 8c.03-2.72 1.49-6 4.5-6C9.37 2 11 3.8 11 8c0 1.25-.38 2.5-1 3.75">
                        </path>
                        <path
                            d="M20 19v-3c0-2.5-2-4-2-4s-2 2-4 2h-2c-2 0-4-2-4-2s-2 1.5-2 4v3c0 1 1 2 2 2h10c1 0 2-1 2-2Z">
                        </path>
                    </svg>
                </div>
                <h3>Carbon Calculator</h3>
                <p>Track and reduce your daily carbon footprint.</p>
            </div>

            <!-- card 3 -->
            <div class="feature-card">
                <div class="icon-circle">
                    <!-- SVG Icon: Recycle -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <path d="M12 15V3"></path>
                        <path d="M3 21h18"></path>
                        <path d="M7 10l-4 4"></path>
                        <path d="M17 10l4 4"></path>
                    </svg>
                </div>
                <h3>DIY Upcycling Hub</h3>
                <p>Share ideas to turn waste into useful items.</p>
            </div>

            <!-- card 4 -->
            <div class="feature-card">
                <div class="icon-circle">
                    <!-- SVG Icon: Award -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="7"></circle>
                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                    </svg>
                </div>
                <h3>Leaderboard & Badges</h3>
                <p>Earn recognition for your sustainable actions.</p>
            </div>

            <!-- card 5 -->
            <div class="feature-card">
                <div class="icon-circle">
                    <!-- SVG Icon: Gift -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 12 20 22 4 22 4 12"></polyline>
                        <rect x="2" y="7" width="20" height="5"></rect>
                        <line x1="12" y1="22" x2="12" y2="7"></line>
                        <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                        <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
                    </svg>
                </div>
                <h3>Rewards System</h3>
                <p>Redeem points for eco-friendly products.</p>
            </div>
        </div>
    </section>

    <!-- about Us Section -->
    <section class="about" id="about">
        <h2 class="section-title" style="text-align: center; margin-bottom: 60px;">About Us</h2>

        <div class="about-container">
            <div class="about-image">
                <!-- Comm picture -->
                <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1200&q=80"
                    alt="Community">
            </div>

            <div class="about-content">
                <div class="about-item">
                    <h3><span class="number-badge">1</span> Promote Green Lifestyle</h3>
                    <p>EcoLeaf encourages students to adopt eco-friendly habits and make conscious daily choices to
                        reduce waste.</p>
                </div>

                <div class="about-item">
                    <h3><span class="number-badge">2</span> Build a Sustainable Community</h3>
                    <p>We create a connected space where students can share ideas, redeem upcycled items and support
                        each other.</p>
                </div>

                <div class="about-item">
                    <h3><span class="number-badge">3</span> Reward Positive Actions</h3>
                    <p>Through badges, Green Points, and campus-wide recognition, EcoLeaf makes sustainability fun and
                        engaging.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <footer>
        <div class="footer-grid">
            <!-- Brand Col -->
            <div class="footer-brand">
                <div class="logo">EcoLeaf</div>
                <p>Empowering students to build a sustainable future, one action at a time.</p>
            </div>

            <!-- Link Col 1 -->
            <div class="footer-col">
                <h4>Platform</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Leaderboard</a></li>
                    <li><a href="#">Events</a></li>
                </ul>
            </div>

            <!-- Link Col 2 -->
            <div class="footer-col">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>

            <!-- Connect Col -->
            <div class="footer-col">
                <h4>Connect With Us</h4>
                <div class="social-icons">
                    <div class="social-circle">FB</div>
                    <div class="social-circle">IG</div>
                    <div class="social-circle">TW</div>
                    <div class="social-circle">LI</div>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>