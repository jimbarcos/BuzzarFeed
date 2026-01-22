<?php
/*
PROGRAM NAME: Bootstrap File (bootstrap.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It initializes the application environment, loads configurations, sets up autoloading for dependencies, and provides utility helpers for component rendering.
It is required by all pages and services to ensure consistent initialization and environment setup.

DATE CREATED: December 2, 2025
LAST MODIFIED: December 2, 2025

PURPOSE:
The purpose of this program is to prepare the BuzzarFeed application for execution.
It ensures configuration files are loaded, external dependencies via Composer are autoloaded, and the project's PSR-4 autoloader is registered for the BuzzarFeed namespace.
Additionally, it sets up session handling and error management for production or development environments.
Helper functions for component creation and rendering are also defined for consistent UI rendering.

DATA STRUCTURES:
- SRC_PATH (constant): Base path to the `src` directory.
- DEVELOPMENT_MODE (constant): Boolean flag indicating development or production environment.
- Autoloader (PSR-4 compliant): Dynamically loads classes under the `BuzzarFeed` namespace.
- component($name, $props) (function): Factory helper to create UI components.
- render($component) (function): Helper to render BaseComponent instances or raw HTML strings.

ALGORITHM / LOGIC:
1. Check if configuration file exists; terminate with error message if missing.
2. Include `config.php` to load system-wide settings.
3. Load Composer autoloader (`vendor/autoload.php`) for external dependencies like PHPMailer.
4. Register PSR-4 autoloader for `BuzzarFeed` namespace:
   a. Check if class belongs to `BuzzarFeed` namespace.
   b. Convert namespace to file path relative to `src/`.
   c. Include class file if it exists.
5. Include `Session.php` utility:
   a. Verify the file exists.
   b. Require it for session handling (pages start sessions when needed).
6. Set a custom error handler for production to log errors without displaying them.
7. Define helper functions:
   a. `component($name, $props)`: creates component instances using the ComponentFactory.
   b. `render($component)`: outputs the rendered component or raw HTML string.

NOTES:
- Session is not auto-started; pages can start sessions explicitly using `Session::start()`.
- Error handler only overrides default behavior when not in development mode.
- This file should be included at the top of all pages to ensure proper initialization.
- Helper functions simplify component creation and rendering for the frontend.
- Future enhancements could include configuration caching or more flexible error handling.
*/

// Load configuration
if (!file_exists(__DIR__ . '/config/config.php')) {
    die('Error: config/config.php not found at ' . __DIR__ . '/config/config.php');
}

try {
    require_once __DIR__ . '/config/config.php';
} catch (Exception $e) {
    die('Error loading config: ' . $e->getMessage());
}

// Load Composer autoloader (for PHPMailer and other dependencies)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// PSR-4 Autoloader for BuzzarFeed namespace
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'BuzzarFeed\\';
    
    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';
    
    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Replace namespace separators with directory separators
    // and append with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
});

// Start session
if (!file_exists(SRC_PATH . '/utils/Session.php')) {
    die('Error: Session.php not found at ' . SRC_PATH . '/utils/Session.php');
}

try {
    require_once SRC_PATH . '/utils/Session.php';
    // Don't auto-start session - let pages start it when needed
    // \BuzzarFeed\Utils\Session::start();
} catch (Exception $e) {
    die('Error loading Session: ' . $e->getMessage());
}

// Set up error handler for production
if (!DEVELOPMENT_MODE) {
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        error_log("Error [{$errno}]: {$errstr} in {$errfile} on line {$errline}");
        return true;
    });
}

// Helper function to load components
function component($name, $props = []) {
    return \BuzzarFeed\Components\ComponentFactory::create($name, $props);
}

// Helper function to render components
function render($component) {
    if ($component instanceof \BuzzarFeed\Components\BaseComponent) {
        echo $component->render();
    } elseif (is_string($component)) {
        echo $component;
    }
}
