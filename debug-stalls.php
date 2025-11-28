<?php
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
