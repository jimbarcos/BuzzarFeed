<?php
/*
PROGRAM NAME: Terms and Services Page (terms.php)

PROGRAMMER: Frontend and Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed web application. It is a public-facing
informational page that displays the Terms and Services for different types
of users, including general users, food stall owners, and food enthusiasts.

DATE CREATED: December 10, 2025
LAST MODIFIED: December 10, 2025

PURPOSE:
The purpose of this program is to present the official Terms and Services of
BuzzarFeed. It ensures that users are informed of their rights, responsibilities,
and limitations when using the platform. The page dynamically switches content
based on the selected user category.

DATA STRUCTURES:
- $pageTitle (string): Stores the page title for the HTML document
- $pageDescription (string): Meta description for SEO purposes
- $activeSection (string): Determines which Terms section is currently active
  (general, owners, enthusiasts)

ALGORITHM / LOGIC:
1. Enable error reporting for debugging during development.
2. Load system bootstrap file and required utility classes.
3. Start a user session.
4. Retrieve the active terms section from the URL query parameter.
5. Display the appropriate Terms and Services content based on the active section.
6. Render shared header and footer components for consistent layout.

NOTES:
- This page does not modify user data.
- Content visibility is controlled using conditional CSS classes.
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/bootstrap.php';

use BuzzarFeed\Utils\Helpers;
use BuzzarFeed\Utils\Session;

// Start session
Session::start();

$pageTitle = "Terms and Services - BuzzarFeed";
$pageDescription = "Terms and conditions for using BuzzarFeed";

// Get active section from query parameter
$activeSection = Helpers::get('section', 'general'); // general, owners, enthusiasts
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= Helpers::escape($pageDescription) ?>">
    <title><?= Helpers::escape($pageTitle) ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= IMAGES_URL ?>/favicon.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= CSS_URL ?>/variables.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/components/button.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/components/forms.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/components/dropdown.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/styles.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/terms.css">
</head>
<body class="terms-page">
    <!-- Header -->
    <?php require INCLUDES_PATH . '/header.php'; ?>

    <main class="terms-container">
        <div class="terms-header">
            <h1>Terms and Services</h1>
            <p>Please read these terms carefully before using BuzzarFeed</p>
        </div>

        <div class="terms-layout">
            <!-- Sidebar Navigation -->
            <aside class="terms-sidebar">
                <ul class="terms-nav">
                    <li>
                        <a href="?section=general" class="nav-link <?= $activeSection === 'general' ? 'active' : '' ?>">
                            <i class="fas fa-file-contract"></i>
                            General Terms
                        </a>
                    </li>
                    <li>
                        <a href="?section=owners" class="nav-link <?= $activeSection === 'owners' ? 'active' : '' ?>">
                            <i class="fas fa-store"></i>
                            Food Stall Owners
                        </a>
                    </li>
                    <li>
                        <a href="?section=enthusiasts" class="nav-link <?= $activeSection === 'enthusiasts' ? 'active' : '' ?>">
                            <i class="fas fa-utensils"></i>
                            Food Enthusiasts
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content -->
            <div class="terms-content">
                <!-- General Terms Section -->
                <section class="content-section <?= $activeSection === 'general' ? 'active' : '' ?>">
                    <h1 class="section-title">General Terms of Service</h1>

                    <h2>1. Acceptance of Terms</h2>
                    <p>
                        By accessing and using BuzzarFeed, you accept and agree to be bound by the terms and provision 
                        of this agreement. If you do not agree to these terms, please do not use our service.
                    </p>

                    <h2>2. Description of Service</h2>
                    <p>
                        BuzzarFeed is a platform that connects food enthusiasts with local food stalls. We provide:
                    </p>
                    <ul>
                        <li>A directory of registered food stalls</li>
                        <li>User reviews and ratings</li>
                        <li>Stall registration and management tools</li>
                        <li>Interactive maps and location services</li>
                    </ul>

                    <h2>3. User Accounts</h2>
                    <h3>3.1 Account Creation</h3>
                    <ul>
                        <li>You must provide accurate and complete information when creating an account</li>
                        <li>You are responsible for maintaining the confidentiality of your account credentials</li>
                        <li>You must be at least 13 years old to create an account</li>
                        <li>One person may not maintain more than one account</li>
                    </ul>

                    <h3>3.2 Account Security</h3>
                    <ul>
                        <li>You are responsible for all activities that occur under your account</li>
                        <li>Notify us immediately of any unauthorized use of your account</li>
                        <li>We reserve the right to suspend or terminate accounts that violate our terms</li>
                    </ul>

                    <h2>4. User Conduct</h2>
                    <p>You agree not to:</p>
                    <ul>
                        <li>Post false, misleading, or fraudulent information</li>
                        <li>Harass, abuse, or harm other users</li>
                        <li>Violate any applicable laws or regulations</li>
                        <li>Attempt to gain unauthorized access to our systems</li>
                        <li>Use automated systems to access the service without permission</li>
                        <li>Post spam or unsolicited promotional content</li>
                    </ul>

                    <h2>5. Intellectual Property</h2>
                    <p>
                        All content on BuzzarFeed, including text, graphics, logos, and software, is the property of 
                        BuzzarFeed or its content suppliers and is protected by intellectual property laws.
                    </p>

                    <h2>6. Privacy</h2>
                    <p>
                        Your use of BuzzarFeed is also governed by our Privacy Policy. We collect and use your 
                        information as described in our Privacy Policy to provide and improve our services.
                    </p>

                    <h2>7. Limitation of Liability</h2>
                    <p>
                        BuzzarFeed is provided "as is" without warranties of any kind. We are not liable for any 
                        damages arising from your use of the service, including but not limited to direct, indirect, 
                        incidental, or consequential damages.
                    </p>

                    <h2>8. Modifications to Terms</h2>
                    <p>
                        We reserve the right to modify these terms at any time. We will notify users of significant 
                        changes. Your continued use of BuzzarFeed after changes constitutes acceptance of the new terms.
                    </p>

                    <h2>9. Termination</h2>
                    <p>
                        We may terminate or suspend your account and access to the service at our sole discretion, 
                        without notice, for conduct that we believe violates these terms or is harmful to other users, 
                        us, or third parties.
                    </p>

                    <h2>10. Contact Information</h2>
                    <p>
                        For questions about these terms, please contact us at 
                        <strong>support@buzzarfeed.com</strong>
                    </p>

                    <div class="last-updated">
                        <i class="fas fa-clock"></i> Last updated: December 10, 2025
                    </div>
                </section>

                <!-- Food Stall Owners Section -->
                <section class="content-section <?= $activeSection === 'owners' ? 'active' : '' ?>">
                    <h1 class="section-title">Terms for Food Stall Owners</h1>

                    <div class="info-box">
                        <h4><i class="fas fa-info-circle"></i> Important Notice</h4>
                        <p>
                            As a food stall owner on BuzzarFeed, you have additional responsibilities beyond our 
                            general terms. Please read these carefully.
                        </p>
                    </div>

                    <h2>1. Stall Registration</h2>
                    <h3>1.1 Application Requirements</h3>
                    <ul>
                        <li>All information provided in your stall application must be accurate and truthful</li>
                        <li>You must have legal authority to operate the food stall you are registering</li>
                        <li>You must provide valid business documentation when requested</li>
                        <li>Your stall must comply with all local health and safety regulations</li>
                    </ul>

                    <h3>1.2 Approval Process</h3>
                    <ul>
                        <li>All stall registrations are subject to review and approval by BuzzarFeed administrators</li>
                        <li>We reserve the right to reject any application without providing a reason</li>
                        <li>Approval does not constitute endorsement of your food stall</li>
                        <li>You will be notified of the application status via email</li>
                    </ul>

                    <h2>2. Stall Information Management</h2>
                    <h3>2.1 Accuracy of Information</h3>
                    <ul>
                        <li>You must maintain accurate and up-to-date information about your stall</li>
                        <li>This includes operating hours, menu items, prices, and contact information</li>
                        <li>Location information must be precise and accurate</li>
                        <li>Update your profile immediately if any information changes</li>
                    </ul>

                    <h3>2.2 Menu and Pricing</h3>
                    <ul>
                        <li>Menu items and prices listed must reflect what you actually offer</li>
                        <li>Clearly indicate if items are seasonal or temporarily unavailable</li>
                        <li>Allergies and dietary information must be accurate and complete</li>
                        <li>You are responsible for updating menu information regularly</li>
                    </ul>

                    <h3>2.3 Photos and Media</h3>
                    <ul>
                        <li>Photos must be of your actual food and stall</li>
                        <li>Do not use stock photos or photos from other establishments</li>
                        <li>Images must be appropriate and not misleading</li>
                        <li>You grant BuzzarFeed license to use uploaded photos for promotional purposes</li>
                    </ul>

                    <h2>3. Customer Reviews</h2>
                    <h3>3.1 Receiving Reviews</h3>
                    <ul>
                        <li>All users can leave reviews about your stall</li>
                        <li>Reviews reflect individual customer experiences and opinions</li>
                        <li>You cannot remove reviews simply because they are negative</li>
                        <li>Reviews that violate our policies can be reported for moderation</li>
                    </ul>

                    <h3>3.2 Responding to Reviews</h3>
                    <ul>
                        <li>You are encouraged to respond professionally to customer feedback</li>
                        <li>Do not harass or threaten reviewers</li>
                        <li>Do not offer incentives to remove or modify negative reviews</li>
                        <li>Use reviews as constructive feedback to improve your service</li>
                    </ul>

                    <div class="warning-box">
                        <h4><i class="fas fa-exclamation-triangle"></i> Prohibited Actions</h4>
                        <p>The following actions will result in immediate suspension or termination:</p>
                        <ul style="margin-bottom: 0;">
                            <li>Creating fake accounts to leave positive reviews</li>
                            <li>Offering payments or incentives for positive reviews</li>
                            <li>Threatening or harassing customers who leave negative reviews</li>
                            <li>Asking friends or family to flood your profile with fake reviews</li>
                        </ul>
                    </div>

                    <h2>4. Legal Compliance</h2>
                    <h3>4.1 Business Licenses and Permits</h3>
                    <ul>
                        <li>You must maintain all necessary business licenses and permits</li>
                        <li>Comply with all local, state, and federal regulations</li>
                        <li>Maintain proper food handling certifications</li>
                        <li>BuzzarFeed may request proof of compliance at any time</li>
                    </ul>

                    <h3>4.2 Health and Safety</h3>
                    <ul>
                        <li>Maintain proper health and safety standards</li>
                        <li>Follow food safety guidelines and regulations</li>
                        <li>Properly handle and store food products</li>
                        <li>Maintain clean and sanitary conditions</li>
                    </ul>

                    <h2>5. Financial Terms</h2>
                    <h3>5.1 Service Fees</h3>
                    <p>
                        Currently, BuzzarFeed does not charge listing fees for food stalls. We reserve the right 
                        to introduce fees in the future with advance notice to all stall owners.
                    </p>

                    <h3>5.2 Payment Processing</h3>
                    <p>
                        BuzzarFeed does not process payments between customers and stalls. All transactions occur 
                        directly between you and your customers. You are responsible for your own payment processing.
                    </p>

                    <h2>6. Liability and Indemnification</h2>
                    <h3>6.1 Your Responsibility</h3>
                    <ul>
                        <li>You are solely responsible for the quality and safety of your food</li>
                        <li>You are responsible for any harm caused by your products or services</li>
                        <li>BuzzarFeed is not liable for any disputes between you and your customers</li>
                        <li>You are responsible for maintaining adequate insurance coverage</li>
                    </ul>

                    <h3>6.2 Indemnification</h3>
                    <p>
                        You agree to indemnify and hold BuzzarFeed harmless from any claims, damages, or expenses 
                        arising from your use of the platform, your food products, or your violation of these terms.
                    </p>

                    <h2>7. Account Suspension and Termination</h2>
                    <h3>7.1 Grounds for Suspension</h3>
                    <p>Your stall may be suspended or removed for:</p>
                    <ul>
                        <li>Violation of these terms</li>
                        <li>Multiple customer complaints about safety or quality</li>
                        <li>Fraudulent or deceptive practices</li>
                        <li>Failure to maintain required licenses or permits</li>
                        <li>Health code violations</li>
                    </ul>

                    <h3>7.2 Appeal Process</h3>
                    <p>
                        If your stall is suspended, you may appeal by contacting support@buzzarfeed.com. 
                        Include your stall ID and a detailed explanation. We will review appeals within 7 business days.
                    </p>

                    <div class="last-updated">
                        <i class="fas fa-clock"></i> Last updated: December 10, 2025
                    </div>
                </section>

                <!-- Food Enthusiasts Section -->
                <section class="content-section <?= $activeSection === 'enthusiasts' ? 'active' : '' ?>">
                    <h1 class="section-title">Terms for Food Enthusiasts</h1>

                    <div class="info-box">
                        <h4><i class="fas fa-info-circle"></i> Welcome Food Lovers!</h4>
                        <p>
                            As a food enthusiast on BuzzarFeed, you play a vital role in our community. 
                            Your reviews and feedback help others discover great food and help stalls improve their service.
                        </p>
                    </div>

                    <h2>1. Using BuzzarFeed</h2>
                    <h3>1.1 Browse and Discover</h3>
                    <ul>
                        <li>Explore food stalls in your area</li>
                        <li>Read reviews from other food enthusiasts</li>
                        <li>Use our map feature to find nearby stalls</li>
                        <li>Save your favorite stalls for easy access</li>
                    </ul>

                    <h3>1.2 Account Features</h3>
                    <ul>
                        <li>Create a personalized profile</li>
                        <li>Track your review history</li>
                        <li>Build your reputation in the community</li>
                        <li>Receive notifications about your favorite stalls</li>
                    </ul>

                    <h2>2. Writing Reviews</h2>
                    <h3>2.1 Review Guidelines</h3>
                    <ul>
                        <li>Only review stalls you have personally visited</li>
                        <li>Base your review on your actual experience</li>
                        <li>Be honest, fair, and constructive in your feedback</li>
                        <li>Include specific details about food quality, service, and atmosphere</li>
                    </ul>

                    <h3>2.2 What Makes a Good Review</h3>
                    <ul>
                        <li><strong>Be specific:</strong> Mention which dishes you tried</li>
                        <li><strong>Be balanced:</strong> Include both positives and areas for improvement</li>
                        <li><strong>Be helpful:</strong> Provide information that helps other diners make decisions</li>
                        <li><strong>Be fair:</strong> Consider factors like price, service speed, and portion sizes</li>
                        <li><strong>Be timely:</strong> Write your review soon after your visit</li>
                    </ul>

                    <h3>2.3 Review Content Standards</h3>
                    <p>Your reviews must not contain:</p>
                    <ul>
                        <li>Offensive, abusive, or discriminatory language</li>
                        <li>Personal attacks on staff or owners</li>
                        <li>Unverified health or safety claims</li>
                        <li>Promotional content or advertising</li>
                        <li>Threats or harassment</li>
                        <li>Profanity or vulgar language</li>
                    </ul>

                    <div class="warning-box">
                        <h4><i class="fas fa-exclamation-triangle"></i> Prohibited Review Practices</h4>
                        <p>The following will result in review removal and possible account suspension:</p>
                        <ul style="margin-bottom: 0;">
                            <li>Writing reviews for stalls you haven't visited</li>
                            <li>Accepting payment or incentives for positive reviews</li>
                            <li>Writing fake reviews to harm competitors</li>
                            <li>Creating multiple accounts to leave multiple reviews</li>
                            <li>Plagiarizing reviews from other sources</li>
                        </ul>
                    </div>

                    <h2>3. Rating System</h2>
                    <h3>3.1 How Ratings Work</h3>
                    <ul>
                        <li>Ratings range from 1 to 5 stars</li>
                        <li>1 star = Poor, 2 stars = Fair, 3 stars = Good, 4 stars = Very Good, 5 stars = Excellent</li>
                        <li>Your rating should reflect your overall experience</li>
                        <li>Consider food quality, service, value, and atmosphere</li>
                    </ul>

                    <h3>3.2 Rating Guidelines</h3>
                    <ul>
                        <li>Don't give 1 star just because of one minor issue</li>
                        <li>Reserve 5 stars for truly exceptional experiences</li>
                        <li>Be consistent in how you rate different establishments</li>
                        <li>Explain your rating in your written review</li>
                    </ul>

                    <h2>4. Review Reactions and Engagement</h2>
                    <h3>4.1 Reacting to Reviews</h3>
                    <ul>
                        <li>You can mark reviews as "Helpful" or "Not Helpful"</li>
                        <li>Use reactions to highlight quality reviews</li>
                        <li>Don't abuse the reaction system to harm legitimate reviews</li>
                    </ul>

                    <h3>4.2 Reporting Reviews</h3>
                    <p>You can report reviews that:</p>
                    <ul>
                        <li>Contain false or fraudulent information</li>
                        <li>Violate our content standards</li>
                        <li>Appear to be spam or promotional</li>
                        <li>Include harassment or threats</li>
                    </ul>

                    <h2>5. Community Interaction</h2>
                    <h3>5.1 Respectful Communication</h3>
                    <ul>
                        <li>Treat other users with respect</li>
                        <li>Engage in constructive discussions</li>
                        <li>Respect differing opinions and experiences</li>
                        <li>Don't harass users who disagree with your reviews</li>
                    </ul>

                    <h3>5.2 Building Reputation</h3>
                    <ul>
                        <li>Write thoughtful, detailed reviews to build credibility</li>
                        <li>Your review history is visible to other users</li>
                        <li>Consistent, quality contributions enhance your reputation</li>
                        <li>Help maintain a trustworthy community</li>
                    </ul>

                    <h2>6. Privacy and Personal Information</h2>
                    <h3>6.1 What We Collect</h3>
                    <ul>
                        <li>Your account information (name, email)</li>
                        <li>Reviews, ratings, and interactions</li>
                        <li>Location data (when you use map features)</li>
                        <li>Usage data and preferences</li>
                    </ul>

                    <h3>6.2 How We Use Your Information</h3>
                    <ul>
                        <li>To provide and improve our services</li>
                        <li>To personalize your experience</li>
                        <li>To communicate with you about your account</li>
                        <li>To prevent fraud and abuse</li>
                    </ul>

                    <h3>6.3 Your Privacy Rights</h3>
                    <ul>
                        <li>You can update your profile information at any time</li>
                        <li>You can delete your reviews</li>
                        <li>You can request account deletion</li>
                        <li>We will never sell your personal information</li>
                    </ul>

                    <h2>7. Content Moderation</h2>
                    <h3>7.1 Review Moderation</h3>
                    <ul>
                        <li>All reviews are subject to moderation</li>
                        <li>We may remove reviews that violate our terms</li>
                        <li>Repeated violations may result in account suspension</li>
                        <li>You will be notified if your review is removed</li>
                    </ul>

                    <h3>7.2 Appeals</h3>
                    <p>
                        If you believe a review was unfairly removed, you may appeal by contacting 
                        support@buzzarfeed.com within 14 days. Include the review ID and explain why 
                        you believe it should be reinstated.
                    </p>

                    <h2>8. Best Practices for Food Enthusiasts</h2>
                    <div class="info-box">
                        <h4><i class="fas fa-lightbulb"></i> Tips for Being a Great Reviewer</h4>
                        <ul style="margin-bottom: 0;">
                            <li><strong>Visit during normal hours:</strong> Don't judge a stall based on a rush hour visit</li>
                            <li><strong>Try signature dishes:</strong> Order what the stall is known for</li>
                            <li><strong>Be patient:</strong> Small stalls may take time, especially when busy</li>
                            <li><strong>Consider context:</strong> Compare stalls to similar establishments, not fine dining</li>
                            <li><strong>Update reviews:</strong> If a stall improves or declines, update your review</li>
                            <li><strong>Include photos:</strong> Pictures help others visualize their experience</li>
                            <li><strong>Mention dietary options:</strong> Note vegetarian, vegan, or allergen-free options</li>
                            <li><strong>Be constructive:</strong> Suggest improvements rather than just complaining</li>
                        </ul>
                    </div>

                    <h2>9. Disclaimer</h2>
                    <h3>9.1 User-Generated Content</h3>
                    <p>
                        Reviews on BuzzarFeed represent individual opinions and experiences. BuzzarFeed does not 
                        verify the accuracy of user reviews and is not responsible for their content.
                    </p>

                    <h3>9.2 Stall Information</h3>
                    <p>
                        While we strive to maintain accurate information, stalls may change hours, menus, or locations 
                        without notice. Always verify critical information directly with the stall.
                    </p>

                    <h3>9.3 Health and Safety</h3>
                    <p>
                        BuzzarFeed is not responsible for food safety, quality, or health issues that may arise from 
                        visiting listed establishments. Always use your judgment and follow health guidelines.
                    </p>

                    <div class="last-updated">
                        <i class="fas fa-clock"></i> Last updated: December 10, 2025
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php require INCLUDES_PATH . '/footer.php'; ?>

    <!-- JavaScript -->
    <script type="module" src="<?= JS_URL ?>/app.js"></script>
</body>
</html>
