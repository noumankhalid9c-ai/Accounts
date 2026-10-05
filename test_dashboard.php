<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    app()->make(App\Http\Controllers\DashboardController::class)->index();
    echo "SUCCESS\n";
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
