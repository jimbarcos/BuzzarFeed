<?php
/*
PROGRAM NAME: Logout Handler (logout.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It handles user logout functionality, including session termination, cleanup, and redirection to the homepage.

DATE CREATED: December 2, 2025
LAST MODIFIED: December 2, 2025

PURPOSE:
The purpose of this program is to securely log out a user from the BuzzarFeed platform. It terminates the current session, 
clears all session data to prevent any unauthorized access, and sets a flash message to inform the user that they have successfully 
logged out. Finally, the program redirects the user back to the homepage, ensuring a smooth and secure exit from the system.


DATA STRUCTURES:
- Session (class): Handles session management, including start, destroy, and flash message operations.
- Helpers (class): Provides utility functions, including page redirection.
- Flash message:
  - Key: Stores the message type ('success', 'error', etc.)
  - Value: Stores the message content string.

ALGORITHM / LOGIC:
1. Include system bootstrap for loading core utilities and configurations.
2. Start a session using Session::start() if not already active.
3. Destroy the current session using Session::destroy():
   - Clears all session variables.
   - Invalidates the session cookie.
4. Restart the session to set a flash message:
   - Session::setFlash(message, type) sets a one-time notification.
5. Redirect the user to 'index.php' (homepage) using Helpers::redirect().

NOTES:
- Flash message is intended to be displayed on the homepage after redirect.
- Session::start() is called twice intentionally:
   - First to access the current session for destruction.
   - Second to create a fresh session for setting the flash message.
- This script does not require any form input; it performs logout on page load.
- Future enhancements may include logging user logout events for analytics or security.
*/


require_once __DIR__ . '/bootstrap.php';

use BuzzarFeed\Utils\Session;
use BuzzarFeed\Utils\Helpers;

// Start session
Session::start();

// Destroy session
Session::destroy();

// Set flash message
Session::start();
Session::setFlash('You have been logged out successfully.', 'success');

// Redirect to homepage
Helpers::redirect('index.php');
