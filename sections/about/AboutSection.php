<?php
/*
PROGRAM NAME: About Page – What We’re About Section Partial (about-section.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It functions as a view partial for the About page and provides a narrative overview of the platform’s mission, purpose, and core offerings.
The section is included within the About page layout and focuses on descriptive content rather than user interaction or dynamic logic.
It relies on static assets and predefined text content to communicate BuzzarFeed’s identity.

DATE CREATED: November 23, 2025
LAST MODIFIED: December 4, 2025

PURPOSE:
The purpose of this program is to explain what BuzzarFeed is about and how it serves food lovers exploring the BGC Night Market.
It introduces the platform’s mission, highlights what it offers to users, and reinforces its role as a discovery and community-driven food guide.
This section helps users understand the value of the platform before engaging with its features.

DATA STRUCTURES:
- Static HTML content for titles, descriptions, and informational cards.
- Image assets referenced via relative paths for icons and decorative elements.

ALGORITHM / LOGIC:
1. Render the section header containing the title and mission description.
2. Display a grid-based layout of informational cards describing platform offerings.
3. Load and display icon images for each card.
4. Render decorative visual elements to enhance layout and branding.

NOTES:
- This file is a presentation-only partial with no dynamic data processing.
- Content is static and intended for storytelling and branding purposes.
- Asset paths are relative to the About page directory.
- Styling and layout behavior are controlled entirely through CSS.
- Future enhancements may include dynamic content, animations, or localization support.
*/
?>

<section class="about-section">
    <div class="about-header">
        <h2 class="about-title">
            <span class="green">What</span> we're<br>
            all about
        </h2>
        <p class="about-description">
            To connect food lovers with the vibrant stalls and hidden gems that make the night market buzz with life. From crispy street snacks to hearty rice meals, from sweet indulgences to refreshing drinks, every stall has a story — and we're here to help you discover it.
        </p>
    </div>
    
    <div class="about-content">
        <div class="about-grid">
            <div class="about-card orange">
                <div class="card-icon">
                    <img src="assets/images/about/about-offer.png" alt="What we do">
                </div>
                <h3 class="card-title">What we do</h3>
                <p class="card-text">
                    Browse through a curated list of vendors, each with their menus and specialties. Quickly find stalls by name, dish, or cuisine. See what other foodies are saying and share your own experiences.
                </p>
            </div>
            
            <div class="about-card green">
                <div class="card-icon">
                    <img src="assets/images/about/about-offer.png" alt="What we do">
                </div>
                <h3 class="card-title">What we do</h3>
                <p class="card-text">
                    Browse through a curated list of vendors, each with their menus and specialties. Quickly find stalls by name, dish, or cuisine. See what other foodies are saying and share your own experiences.
                </p>
            </div>
            
            <div class="about-card green">
                <div class="card-icon">
                    <img src="assets/images/about/about-offer.png" alt="What we do">
                </div>
                <h3 class="card-title">What we do</h3>
                <p class="card-text">
                    Browse through a curated list of vendors, each with their menus and specialties. Quickly find stalls by name, dish, or cuisine. See what other foodies are saying and share your own experiences.
                </p>
            </div>
            
            <div class="orange-star">
                <img src="assets/images/about/about-hexadecagon-2.png" alt="Decorative star">
            </div>
        </div>
    </div>
</section>