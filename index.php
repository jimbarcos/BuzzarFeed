<?php
/**
 * BuzzarFeed - Homepage
 * 
 * Main landing page for the BuzzarFeed food blog and review platform
 * Displays hero section, featured stalls, reviews, and signup CTAs
 * 
 * @package BuzzarFeed
 * @version 1.0
 * @author BuzzarFeed Development Team
 * @date October 2025
 */

// Start session for user authentication
session_start();

// Include configuration and database connection (when implemented)
// require_once 'includes/config.php';
// require_once 'includes/db.php';

// Page title for SEO
$pageTitle = "BuzzarFeed - Discover the Flavors of BGC Night Market";
$pageDescription = "Explore food stalls, menus, and honest reviews from fellow food lovers at BGC Night Market Bazaar";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <meta name="keywords" content="BGC Night Market, food stalls, food reviews, Manila food blog, bazaar food">
    <meta name="author" content="BuzzarFeed">
    
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=geist@400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Header Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-container">
            <div class="hero-content">
                <h1 class="hero-title">
                    Discover the Flavors<br>
                    of <span class="highlight-orange">BGC Night Market</span>
                </h1>
                <p class="hero-description">
                    Taste. Try. Savor. Explore top stalls, menus, and honest reviews from fellow food lovers<br>
                    — at BGC Night Market.
                </p>
                <a href="#featured-stalls" class="btn btn-primary btn-discover">
                    <i class="fas fa-utensils"></i> Discover Stalls
                </a>
            </div>
            <div class="hero-image">
                <!-- Placeholder for polaroid-style images -->
                <div class="polaroid-container">
                    <div class="polaroid polaroid-1">
                        <div class="polaroid-img"></div>
                    </div>
                    <div class="polaroid polaroid-2">
                        <div class="polaroid-img"></div>
                    </div>
                    <div class="polaroid polaroid-3">
                        <div class="polaroid-img"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Brands Carousel -->
    <section class="brands-carousel">
        <div class="container">
            <p class="carousel-tagline">
                <span class="highlight-orange">From local favorites to</span> <span class="highlight-green">hidden gems</span> 
                <span class="highlight-orange">— discover them here.</span>
            </p>
            
            <div class="carousel-wrapper">
                <button class="carousel-btn carousel-prev" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="carousel-container">
                    <div class="carousel-track">
                        <!-- Carousel items - these would be populated from database -->
                        <div class="carousel-item">
                            <i class="fas fa-star star-icon"></i>
                            <span class="brand-logo">Logo/brand</span>
                        </div>
                        <div class="carousel-item">
                            <i class="fas fa-star star-icon"></i>
                            <span class="brand-logo">Logo/brand</span>
                        </div>
                        <div class="carousel-item">
                            <i class="fas fa-star star-icon"></i>
                            <span class="brand-logo">Logo/brand</span>
                        </div>
                        <div class="carousel-item">
                            <i class="fas fa-star star-icon"></i>
                            <span class="brand-logo">Logo/brand</span>
                        </div>
                    </div>
                </div>
                
                <button class="carousel-btn carousel-next" aria-label="Next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <div class="carousel-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </section>

    <!-- Featured Stalls Section -->
    <section id="featured-stalls" class="featured-stalls">
        <div class="container">
            <div class="section-header-stalls">
                <h2 class="section-title">
                    <span class="highlight-green">Explore</span> featured<br>stalls
                </h2>
                <p class="section-subtitle">
                    <i class="fas fa-map-marker-alt"></i> Terra 28th, 28th St. corner 7th Ave. BGC
                </p>
                <a href="stalls.php" class="btn btn-secondary btn-browse">
                    Browse <i class="fas fa-arrow-up-right-from-square"></i>
                </a>
            </div>

            <div class="stalls-grid">
                <!-- Featured Stall Card 1 -->
                <article class="stall-card">
                    <div class="stall-card-header">
                        <div class="stall-image-placeholder"></div>
                    </div>
                    <div class="stall-card-overlay">
                        <h3 class="stall-label-name">Kape Kuripot</h3>
                        <p class="stall-label-hours">
                            <i class="far fa-clock"></i> Monday to Saturday 09:00 to 18:00
                        </p>
                        <p class="stall-label-description">
                            Lorem ipsum dolor sit amet. Est magnam possimus in odio quis sed assumenda odio quis impedit.
                        </p>
                    </div>
                    <a href="stall-details.php?id=1" class="btn btn-success stall-card-action">
                        View details
                    </a>
                </article>

                <!-- Featured Stall Card 2 -->
                <article class="stall-card">
                    <div class="stall-card-header">
                        <div class="stall-image-placeholder"></div>
                    </div>
                    <div class="stall-card-overlay">
                        <h3 class="stall-label-name">Kape Kuripot</h3>
                        <p class="stall-label-hours">
                            <i class="far fa-clock"></i> Monday to Saturday 09:00 to 18:00
                        </p>
                        <p class="stall-label-description">
                            Lorem ipsum dolor sit amet. Est magnam possimus in odio quis sed assumenda odio quis impedit.
                        </p>
                    </div>
                    <a href="stall-details.php?id=2" class="btn btn-success stall-card-action">
                        View details
                    </a>
                </article>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews-section">
        <div class="container">
            <h2 class="section-title centered">
                See what foodies are <span class="highlight-green">raving</span> about...
            </h2>

            <div class="reviews-grid">
                <!-- Review Card 1 -->
                <article class="review-card">
                    <div class="reviewer-avatar"></div>
                    <div class="review-content">
                        <h4 class="reviewer-name">Sirap pochi•</h4>
                        <p class="review-text">
                            "Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua et dolore et labore sit ipsum."
                        </p>
                        <div class="review-footer">
                            <div class="review-rating">
                                <span class="rating-value">4.9</span>
                                <i class="fas fa-star"></i>
                            </div>
                            <a href="#" class="btn btn-secondary btn-sm">
                                Read more <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Review Card 2 -->
                <article class="review-card">
                    <div class="reviewer-avatar"></div>
                    <div class="review-content">
                        <h4 class="reviewer-name">H think homemade yung patty sa Yunie•</h4>
                        <p class="review-text">
                            "Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua et dolore et labore sit ipsum."
                        </p>
                        <div class="review-footer">
                            <div class="review-rating">
                                <span class="rating-value">4.9</span>
                                <i class="fas fa-star"></i>
                            </div>
                            <a href="#" class="btn btn-secondary btn-sm">
                                Read more <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Review Card 3 -->
                <article class="review-card">
                    <div class="reviewer-avatar"></div>
                    <div class="review-content">
                        <h4 class="reviewer-name">Sirap pochi•</h4>
                        <p class="review-text">
                            "Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua et dolore et labore sit ipsum."
                        </p>
                        <div class="review-footer">
                            <div class="review-rating">
                                <span class="rating-value">4.9</span>
                                <i class="fas fa-star"></i>
                            </div>
                            <a href="#" class="btn btn-secondary btn-sm">
                                Read more <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Join BuzzarFeed CTA Section -->
    <section class="join-section">
        <div class="container">
            <h2 class="section-title centered">Join BuzzarFeed!</h2>

            <div class="join-cards">
                <!-- Food Enthusiast Card -->
                <article class="join-card join-card-enthusiast">
                    <h3 class="join-card-title">
                        Are you a...<br>
                        <span class="join-card-role">Food Enthusiast</span>
                    </h3>
                    <ul class="join-card-benefits">
                        <li><i class="fas fa-check"></i> Access to a variety food reviews</li>
                        <li><i class="fas fa-check"></i> Write and food review cards</li>
                        <li><i class="fas fa-check"></i> Engage with the foodies</li>
                        <li><i class="fas fa-check"></i> Discover hidden food stalls</li>
                    </ul>
                    <a href="signup.php?type=user" class="btn btn-primary btn-join">
                        Get started
                    </a>
                </article>

                <!-- Food Stall Owner Card -->
                <article class="join-card join-card-owner">
                    <h3 class="join-card-title">
                        Are you a...<br>
                        <span class="join-card-role">Food Stall Owner</span>
                    </h3>
                    <ul class="join-card-benefits">
                        <li><i class="fas fa-check"></i> Access to a variety food reviews</li>
                        <li><i class="fas fa-check"></i> Access to deal called stall pages</li>
                        <li><i class="fas fa-check"></i> Rate food stall operation</li>
                        <li><i class="fas fa-check"></i> Receive full time feature</li>
                    </ul>
                    <a href="signup.php?type=owner" class="btn btn-primary btn-join">
                        Get started
                    </a>
                </article>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>
