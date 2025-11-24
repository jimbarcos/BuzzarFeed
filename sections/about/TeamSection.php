<?php
// Team Section partial for about page

use BuzzarFeed\Utils\Helpers;
?>
<section class="team-section">
    <div class="team-star-1"></div>
    <div class="team-star-2"></div>
    
    <div class="team-container">
        <div class="team-title-box">
            <h2 class="team-title">
                Meet the<br>
                Team
            </h2>
        </div>
        
        <div class="team-grid">
            <?php foreach ($teamMembers as $member): ?>
                <div class="team-member">
                    <div class="member-photo">
                        <i class="fas fa-smile"></i>
                    </div>
                    <div class="member-info">
                        <p class="member-name"><?= Helpers::escape($member['name']) ?></p>
                        <p class="member-position"><?= Helpers::escape($member['position']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
