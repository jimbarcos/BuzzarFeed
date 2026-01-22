<?php
/*
PROGRAM NAME: Stalls Debug (debug-stalls.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It provides a simple debug interface to fetch and display a subset of featured food stalls. 
The page is intended for developers to inspect the data returned by the StallService, both in human-readable and JSON formats.

DATE CREATED: November 29, 2025
LAST MODIFIED: November 29, 2025

PURPOSE:
The purpose of this program is to allow developers to quickly fetch and inspect featured stall data. 
It demonstrates how to use the StallService to retrieve a limited number of random stalls, prints the raw PHP array for debugging, 
and also outputs the same data in JSON format for easier integration with frontend components or API testing. 
This tool helps verify that the service returns correct and complete stall information before rendering it on production pages.

DATA STRUCTURES:
- StallService (class): Provides methods to interact with stall-related data in the database.
- $stallService (object): Instance of StallService used to fetch stall data.
- $stalls (array): Array containing the random featured stalls returned by StallService.
- Output:
  - Preformatted PHP array for easy readability.
  - JSON-encoded string for integration or debugging.

ALGORITHM / LOGIC:
1. Include system bootstrap to load configurations, autoloaders, and services.
2. Import StallService using the `use` statement.
3. Initialize StallService object.
4. Call `$stallService->getRandomStalls(2)` to fetch 2 random featured stalls from the database.
5. Output the fetched data:
   a. Print raw PHP array using `print_r` inside `<pre>` tags for readable formatting.
   b. Print the same data in JSON format using `json_encode` with `JSON_PRETTY_PRINT`.
6. Close `<pre>` tag to maintain clean formatting in browser.

NOTES:
- This script is intended for development/debugging only and should not be exposed publicly.
- The number of stalls fetched is currently hardcoded to 2 but can be modified as needed.
- JSON output is useful for testing API endpoints or feeding frontend components.
- No HTML or CSS styling is applied; output is purely textual for developer inspection.
- Future enhancements could include filtering by category or status, or providing CLI/HTTP options for flexibility.
*/

require_once __DIR__ . '/bootstrap.php';

use BuzzarFeed\Services\StallService;

$stallService = new StallService();
$stalls = $stallService->getRandomStalls(2);

echo "<pre>";
echo "Featured Stalls Data:\n";
echo "====================\n\n";
print_r($stalls);
echo "\n\nJSON Format:\n";
echo json_encode($stalls, JSON_PRETTY_PRINT);
echo "</pre>";
