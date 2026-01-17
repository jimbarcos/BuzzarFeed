<?php
/*
PROGRAM NAME: About Page Hero Section Partial (about-hero-section.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It functions as a view partial for the About page and serves as the introductory hero section of the page.
The section is included within the About page layout and relies on shared configuration constants for asset paths.
It focuses on presenting branded visuals and introductory messaging rather than interactive functionality.

DATE CREATED: November 23, 2025
LAST MODIFIED: November 24, 2025

PURPOSE:
The purpose of this program is to display the hero section of the About page, introducing BuzzarFeed and its role as a digital guide to the BGC Night Market Bazaar.
It establishes the visual identity of the page through decorative imagery, styled typography, and concise introductory text.
This section helps set context and tone before presenting more detailed content about the platform.

DATA STRUCTURES:
- IMAGES_URL (constant): Base path for image assets used within the hero section.

ALGORITHM / LOGIC:
1. Render decorative background shapes positioned on the left and right of the hero section.
2. Load and display branded image assets using the IMAGES_URL constant.
3. Render the main hero title with inline icons and highlighted branding elements.
4. Display a short subtitle describing the purpose of BuzzarFeed.

NOTES:
- This file is a presentation-only partial with no data manipulation logic.
- Decorative images are handled purely for visual design and accessibility text is minimal.
- Asset paths are centralized using the IMAGES_URL constant for maintainability.
- Styling and layout are controlled via CSS.
- Future enhancements may include animations or responsive image variations.
*/
?>

<section class="hero-section">
    <div class="deco-shape hero-left">
        <img src="<?= IMAGES_URL ?>/about/about-hero-left.png" alt="">
    </div>
    <div class="deco-shape hero-right">
        <img src="<?= IMAGES_URL ?>/about/about-hero-right.png" alt="">
    </div>
    
    <div class="hero-content">
        <h1 class="hero-title">
            Everything <span class="icon-stall"><img src="<?= IMAGES_URL ?>/about/about-house.png" alt=""></span> You<br>
            Need<span class="icon-fire"><img src="<?= IMAGES_URL ?>/about/about-hexadecagon.png" alt=""></span>To Know<br>
            About <span class="highlight"><img src="<?= IMAGES_URL ?>/about/about-buzzarfeed.png" alt="BuzzarFeed"></span>
        </h1>
        <p class="hero-subtitle">
            Welcome to BuzzarFeed — your digital guide to the flavors of the BGC Night Market Bazaar.
        </p>
    </div>
</section>
