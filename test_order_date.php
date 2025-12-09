<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the date conversion
$orderDate = '20251208';

echo "Original: {$orderDate}\n";

$converted = \DateTime::createFromFormat('Ymd', $orderDate);
if ($converted) {
    echo "Converted: " . $converted->format('Y-m-d') . "\n";
} else {
    echo "Conversion failed!\n";
    echo "Errors: " . print_r(\DateTime::getLastErrors(), true) . "\n";
}

// Alternative method
$year = substr($orderDate, 0, 4);
$month = substr($orderDate, 4, 2);
$day = substr($orderDate, 6, 2);
$alternative = "{$year}-{$month}-{$day}";
echo "Alternative: {$alternative}\n";
