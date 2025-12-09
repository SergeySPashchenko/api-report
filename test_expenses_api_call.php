<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\SecureSellerService;

try {
    $service = app(SecureSellerService::class);

    echo "Calling getExpenses with dates 2025-12-07...\n";
    $data = $service->getExpenses('2025-12-07', '2025-12-07');

    echo 'Result Type: '.gettype($data)."\n";
    echo 'Result Count: '.count($data)."\n";
    echo 'First Item: '.print_r($data[0] ?? 'None', true)."\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
