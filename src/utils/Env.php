<?php
/*
PROGRAM NAME: Environment Variable Loader (Env.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It is responsible for loading, parsing, and managing environment variables from a `.env` configuration file.
The Env class provides a lightweight alternative to third-party libraries for handling environment configuration, making it suitable for shared hosting environments and controlled deployment setups.
It is typically initialized during application bootstrap and accessed by configuration files, database connectors, and utility classes.

DATE CREATED: November 23, 2025
LAST MODIFIED: November 23, 2025

PURPOSE:
The purpose of this program is to securely load and manage environment-specific configuration values.
It enables separation of sensitive configuration data (such as database credentials and API keys) from source code.
The module ensures consistent access to environment variables throughout the application while supporting type casting, variable interpolation, and default fallbacks.

DATA STRUCTURES:
- self::$variables (array): Stores environment variables loaded from the .env file.
- self::$loaded (bool): Flag indicating whether the .env file has already been loaded.
- $_ENV (superglobal array): Stores environment variables at runtime.
- $_SERVER (superglobal array): Provides server-level access to environment variables.
- Environment variable formats:
  - KEY=value
  - KEY="quoted value"
  - KEY=${OTHER_VARIABLE}

ALGORITHM / LOGIC:
1. Prevent multiple loads by checking the internal loaded flag.
2. Verify that the .env file exists at the provided path.
3. Read the .env file line-by-line, ignoring empty lines and comments.
4. For each valid line:
   a. Split the line into key and value pairs.
   b. Trim whitespace from keys and values.
   c. Remove surrounding quotes from values.
   d. Resolve variable references using the ${VAR_NAME} syntax.
5. Store parsed values in:
   a. Internal variables array.
   b. $_ENV superglobal.
   c. $_SERVER superglobal (if not already defined).
   d. System environment via putenv(), if available.
6. Mark the environment as loaded to prevent reprocessing.
7. Provide helper methods to:
   a. Retrieve environment variables with optional default values.
   b. Check if a variable exists.
   c. Retrieve all loaded variables.
   d. Enforce required variables by throwing exceptions.
8. Automatically cast values to appropriate data types:
   a. Boolean (true/false)
   b. Null
   c. Empty strings
   d. Integers and floats

NOTES:
- This module avoids external dependencies to maintain compatibility with restricted hosting environments.
- Environment variables should never be committed to version control.
- Variable interpolation allows configuration reuse and cleaner .env files.
- Automatic type casting simplifies usage in configuration and logic layers.
- The loader is designed to be executed once during application bootstrap.
- Exceptions are thrown for missing required variables to prevent silent misconfiguration.
- Future enhancements may include encrypted .env support or environment-specific overrides.
*/

namespace BuzzarFeed\Utils;

class Env {
    
    /**
     * @var array Loaded environment variables
     */
    private static array $variables = [];
    
    /**
     * @var bool Whether .env has been loaded
     */
    private static bool $loaded = false;
    
    /**
     * Load environment variables from .env file
     * 
     * @param string $path Path to .env file
     * @return void
     */
    public static function load(string $path): void {
        if (self::$loaded) {
            return;
        }
        
        if (!file_exists($path)) {
            throw new \Exception(".env file not found at: {$path}");
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse line
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes from value
                $value = self::removeQuotes($value);
                
                // Parse variable references like ${VAR_NAME}
                $value = self::parseVariableReferences($value);
                
                // Store in both our array and $_ENV
                self::$variables[$key] = $value;
                $_ENV[$key] = $value;
                
                // Also set as server variable
                if (!isset($_SERVER[$key])) {
                    $_SERVER[$key] = $value;
                }
                
                // Set as environment variable (if putenv is available)
                if (function_exists('putenv')) {
                    putenv("{$key}={$value}");
                }
            }
        }
        
        self::$loaded = true;
    }
    
    /**
     * Get an environment variable
     * 
     * @param string $key Variable name
     * @param mixed $default Default value if not found
     * @return mixed Variable value
     */
    public static function get(string $key, $default = null) {
        // Check our loaded variables first
        if (isset(self::$variables[$key])) {
            return self::castValue(self::$variables[$key]);
        }
        
        // Check $_ENV
        if (isset($_ENV[$key])) {
            return self::castValue($_ENV[$key]);
        }
        
        // Check $_SERVER
        if (isset($_SERVER[$key])) {
            return self::castValue($_SERVER[$key]);
        }
        
        // Check getenv()
        $value = getenv($key);
        if ($value !== false) {
            return self::castValue($value);
        }
        
        return $default;
    }
    
    /**
     * Check if an environment variable exists
     * 
     * @param string $key Variable name
     * @return bool
     */
    public static function has(string $key): bool {
        return isset(self::$variables[$key]) 
            || isset($_ENV[$key]) 
            || isset($_SERVER[$key]) 
            || getenv($key) !== false;
    }
    
    /**
     * Get all environment variables
     * 
     * @return array
     */
    public static function all(): array {
        return self::$variables;
    }
    
    /**
     * Remove quotes from value
     * 
     * @param string $value
     * @return string
     */
    private static function removeQuotes(string $value): string {
        // Remove surrounding quotes
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            return substr($value, 1, -1);
        }
        
        return $value;
    }
    
    /**
     * Parse variable references like ${VAR_NAME}
     * 
     * @param string $value
     * @return string
     */
    private static function parseVariableReferences(string $value): string {
        if (strpos($value, '${') === false) {
            return $value;
        }
        
        return preg_replace_callback('/\$\{([A-Z_]+)\}/', function($matches) {
            return self::get($matches[1], $matches[0]);
        }, $value);
    }
    
    /**
     * Cast value to appropriate type
     * 
     * @param string $value
     * @return mixed
     */
    private static function castValue(string $value) {
        // Boolean values
        $lower = strtolower($value);
        if ($lower === 'true' || $lower === '(true)') {
            return true;
        }
        if ($lower === 'false' || $lower === '(false)') {
            return false;
        }
        
        // Null value
        if ($lower === 'null' || $lower === '(null)') {
            return null;
        }
        
        // Empty string
        if ($lower === 'empty' || $lower === '(empty)') {
            return '';
        }
        
        // Numeric values
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float)$value : (int)$value;
        }
        
        return $value;
    }
    
    /**
     * Require an environment variable (throw exception if not found)
     * 
     * @param string $key Variable name
     * @return mixed Variable value
     * @throws \Exception If variable not found
     */
    public static function require(string $key) {
        $value = self::get($key);
        
        if ($value === null) {
            throw new \Exception("Required environment variable '{$key}' is not set");
        }
        
        return $value;
    }
}
