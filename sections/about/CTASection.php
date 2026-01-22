<?php
/*
PROGRAM NAME: About Page Call-to-Action Section Partial (about-cta-section.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It functions as a view partial for the About page, designed to encourage user engagement and platform participation.
The section is included at the end of the About page layout and relies on shared configuration constants for URL routing.
It contains no business logic and focuses solely on user interaction prompts.

DATE CREATED: November 23, 2025
LAST MODIFIED: November 23, 2025

PURPOSE:
The purpose of this program is to present a clear and compelling call-to-action that invites users to join and explore the BuzzarFeed platform.
It motivates visitors to sign up, discover food stalls, and participate in the community through reviews and engagement.
This section serves as a conversion point, guiding users toward meaningful next steps.

DATA STRUCTURES:
- BASE_URL (constant): Base URL used for constructing navigation links.

ALGORITHM / LOGIC:
1. Render the CTA section container.
2. Display a prominent CTA title to capture user attention.
3. Present supporting descriptive text outlining platform benefits.
4. Render primary and secondary CTA buttons linking to sign-up and stall exploration pages.

NOTES:
- This file is a presentation-only partial with no conditional logic.
- Navigation links rely on BASE_URL for maintainable routing.
- Button styling and layout are controlled via CSS classes.
- This section is intentionally simple to reduce friction and improve conversion.
- Future enhancements may include analytics tracking or conditional CTAs based on user state.
*/
?>

<section class="cta-section">
    <h2 class="cta-title">Ready to Join the Buzz?</h2>
    <p class="cta-text">
        Be part of the BuzzarFeed community — discover stalls, write reviews, and<br>
        connect with fellow foodies.
    </p>
    <div class="cta-buttons">
        <a href="<?= BASE_URL ?>signup" class="cta-btn primary">Sign Up Now</a>
        <a href="<?= BASE_URL ?>stalls" class="cta-btn secondary">Explore Stalls</a>
    </div>
</section>
