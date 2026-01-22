<?php
/*
PROGRAM NAME: 503 Service Unavailable Error Page (503.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This page is part of the BuzzarFeed platform and serves as the custom error page for HTTP 503 (Service Unavailable) responses.
It is displayed to users when the system is undergoing maintenance, experiencing high traffic, or temporarily unable to process requests.
The page integrates the standard header and footer components for consistent branding and navigation.

DATE CREATED: December 2, 2025
LAST MODIFIED: December 2, 2025

PURPOSE:
To inform users that the BuzzarFeed platform is temporarily unavailable and provide guidance on next steps.
Key features include:
- Display of 503 error code and descriptive message
- Maintenance notice box for additional information
- Action buttons for page refresh and contacting support
- Inclusion of header and footer for consistent navigation
- Logging of request details for debugging and monitoring purposes

DATA STRUCTURES:
- $pageTitle (string): Title of the error page
- $pageDescription (string): Meta description for SEO
- $errorDetails (array): Contains request and server information for logging purposes
- $_SERVER: Used to capture request URI, method, query string, referrer, user agent, IP, and server name
- Constants:
  - IMAGES_URL, CSS_URL, JS_URL, BASE_URL
- Session: Used to preserve user authentication state on error pages

ALGORITHM / LOGIC:
1. Define `IS_ERROR_PAGE` constant to allow session initialization in header.
2. Include bootstrap file for configuration, autoloading, and helpers.
3. Start PHP session before sending HTTP headers.
4. Send HTTP 503 status code and `Retry-After` header.
5. Log detailed request and server information to error log for monitoring.
6. Define page title and meta description.
7. Render HTML structure:
   a. Include modular CSS and external resources (Google Fonts, Font Awesome).
   b. Apply inline styles specific to error page layout and responsiveness.
   c. Include header and footer partials.
   d. Display 503 error code, icon, descriptive message, and maintenance notice.
   e. Provide action buttons for refresh and contacting support.
8. Include main JavaScript module for interactive behaviors.

NOTES:
- This error page is fully responsive and accessible.
- Future improvements may include dynamic countdown to service availability or automated retry mechanisms.
- Logging ensures developers can track user experience and diagnose service downtime causes.
*/

// Define this as an error page to allow session initialization in header
define('IS_ERROR_PAGE', true);

require_once __DIR__ . '/../bootstrap.php';

use BuzzarFeed\Utils\Helpers;
use BuzzarFeed\Utils\Session;

// Start session BEFORE setting HTTP response code
Session::start();

// Now set 503 header after session is started
http_response_code(503);
header('Retry-After: 3600'); // Retry after 1 hour

// Log detailed error information
$errorDetails = [
    'timestamp' => date('Y-m-d H:i:s'),
    'error_code' => 503,
    'error_type' => 'Service Unavailable',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
    'query_string' => $_SERVER['QUERY_STRING'] ?? 'None',
    'http_referer' => $_SERVER['HTTP_REFERER'] ?? 'Direct access',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
    'retry_after' => '3600 seconds (1 hour)',
];

// Log to error log
error_log("=== 503 SERVICE UNAVAILABLE ERROR ===");
error_log("Timestamp: " . $errorDetails['timestamp']);
error_log("Request URI: " . $errorDetails['request_uri']);
error_log("Request Method: " . $errorDetails['request_method']);
error_log("Query String: " . $errorDetails['query_string']);
error_log("Referrer: " . $errorDetails['http_referer']);
error_log("User Agent: " . $errorDetails['user_agent']);
error_log("IP Address: " . $errorDetails['ip_address']);
error_log("Retry After: " . $errorDetails['retry_after']);
error_log("=====================================");

$pageTitle = "503 - Service Unavailable | BuzzarFeed";
$pageDescription = "Our service is temporarily unavailable";
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
    
    <!-- Modular CSS Architecture -->
    <link rel="stylesheet" href="<?= CSS_URL ?>/variables.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/components/button.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/components/dropdown.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/styles.css">
    
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        main {
            flex: 1 0 auto;
        }
        
        .error-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 80px 20px;
            text-align: center;
        }
        
        .error-code {
            font-size: 150px;
            font-weight: 700;
            color: var(--color-orange);
            line-height: 1;
            margin: 0 0 20px 0;
            text-shadow: 4px 4px 0px rgba(237, 96, 39, 0.2);
        }
        
        .error-icon {
            font-size: 120px;
            color: var(--color-orange);
            margin-bottom: 30px;
            opacity: 0.8;
        }
        
        .error-title {
            font-size: 36px;
            font-weight: 700;
            color: var(--color-text-primary);
            margin: 0 0 20px 0;
        }
        
        .error-description {
            font-size: 18px;
            color: var(--color-text-secondary);
            margin: 0 0 40px 0;
            line-height: 1.6;
        }
        
        .maintenance-box {
            background: linear-gradient(135deg, var(--color-primary-beige) 0%, #FFE0B2 100%);
            border: 2px solid var(--color-orange);
            border-radius: 12px;
            padding: 30px;
            margin: 40px 0;
        }
        
        .maintenance-box h3 {
            font-size: 20px;
            font-weight: 600;
            color: var(--color-text-primary);
            margin: 0 0 15px 0;
        }
        
        .maintenance-box p {
            font-size: 16px;
            color: var(--color-text-secondary);
            margin: 0;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-large {
            padding: 14px 32px;
            font-size: 16px;
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 100px;
            }
            
            .error-icon {
                font-size: 80px;
            }
            
            .error-title {
                font-size: 28px;
            }
            
            .error-description {
                font-size: 16px;
            }
            
            .error-actions {
                flex-direction: column;
            }
            
            .error-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <!-- Main Content -->
    <main>
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-tools"></i>
            </div>
            
            <h1 class="error-code">503</h1>
            
            <h2 class="error-title">Service Temporarily Unavailable</h2>
            
            <p class="error-description">
                We're currently performing maintenance or experiencing high traffic. 
                Like closing up the stall for a quick cleanup, we'll be back serving you shortly!
            </p>
            
            <div class="maintenance-box">
                <h3>We'll be back soon!</h3>
                <p>
                    Our team is working hard to improve your experience. 
                    This maintenance should only take a short while. 
                    Thank you for your patience and understanding!
                </p>
            </div>
            
            <div class="error-actions">
                <button onclick="location.reload()" class="btn btn-primary btn-large">
                    <i class="fas fa-sync-alt"></i> Refresh Page
                </button>
                <a href="mailto:support@buzzarfeed.com" class="btn btn-outline btn-large">
                    <i class="fas fa-envelope"></i> Contact Support
                </a>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script type="module" src="<?= JS_URL ?>/app.js"></script>
</body>
</html>
