<?php
/*
PROGRAM NAME: Team Section Partial (team-section.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It functions as a view partial for the About page, responsible for displaying information about the team behind the platform.
The section is included within the About page layout and relies on pre-defined team member data passed from the parent page.
It uses helper utilities to ensure safe rendering of dynamic content.

DATE CREATED: November 23, 2025
LAST MODIFIED: December 4, 2025

PURPOSE:
The purpose of this program is to visually present BuzzarFeed team members in a structured and engaging layout.
It highlights team names and roles while maintaining a balanced design using grouped positioning and decorative elements.
This section helps establish credibility and transparency by introducing the people behind the platform.

DATA STRUCTURES:
- $teamMembers (array of associative arrays): List of team members containing:
  - name (string)
  - position (string)
- Helpers (class): Provides escape() for safe HTML output.

ALGORITHM / LOGIC:
1. Begin the team section container.
2. Render the first group of team members (up to three).
3. Display the central "Meet the Team" title block.
4. Render decorative visual elements for design emphasis.
5. Render the second group of team members (up to three).
6. Render any remaining team members (up to a maximum defined by layout constraints).
7. Safely output all dynamic text using helper functions.

NOTES:
- This file is a presentation-only partial and contains no business logic.
- Team member images currently use placeholder assets for consistent styling.
- Helpers::escape() is used to prevent XSS vulnerabilities.
- The number of displayed team members is limited by the layout design.
- Decorative elements are purely visual and handled via CSS positioning.
- Future enhancements may include dynamic images per team member or responsive layout adjustments.
*/

use BuzzarFeed\Utils\Helpers;
?>
<section class="team-section">
    <div class="team-container">
        <?php for ($i = 0; $i < 3 && $i < count($teamMembers); $i++): ?>
            <div class="team-member">
                <div class="member-photo">
                    <img src="assets/images/about/team/team-placeholder.png" alt="<?= Helpers::escape($teamMembers[$i]['name']) ?>">
                </div>
                <div class="member-info">
                    <p class="member-name"><?= Helpers::escape($teamMembers[$i]['name']) ?></p>
                    <p class="member-position"><?= Helpers::escape($teamMembers[$i]['position']) ?></p>
                </div>
            </div>
        <?php endfor; ?>
        
        <div class="team-title-box">
            <h2 class="team-title">
                Meet the<br>
                Team
            </h2>
        </div>

        <div class="team-star-1">
            <img src="assets/images/about/about-hexadecagon-2.png" alt="Orange star decoration">
        </div>
        
        <?php for ($i = 3; $i < 6 && $i < count($teamMembers); $i++): ?>
            <div class="team-member">
                <div class="member-photo">
                    <img src="assets/images/about/team/team-placeholder.png" alt="<?= Helpers::escape($teamMembers[$i]['name']) ?>">
                </div>
                <div class="member-info">
                    <p class="member-name"><?= Helpers::escape($teamMembers[$i]['name']) ?></p>
                    <p class="member-position"><?= Helpers::escape($teamMembers[$i]['position']) ?></p>
                </div>
            </div>
        <?php endfor; ?>
        
        <?php for ($i = 6; $i < 10 && $i < count($teamMembers); $i++): ?>
            <div class="team-member">
                <div class="member-photo">
                    <img src="assets/images/about/team/team-placeholder.png" alt="<?= Helpers::escape($teamMembers[$i]['name']) ?>">
                </div>
                <div class="member-info">
                    <p class="member-name"><?= Helpers::escape($teamMembers[$i]['name']) ?></p>
                    <p class="member-position"><?= Helpers::escape($teamMembers[$i]['position']) ?></p>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <div class="team-star-2">
        <img src="assets/images/about/about-hexadecagon-2.png" alt="Orange star decoration">
    </div>
</section>