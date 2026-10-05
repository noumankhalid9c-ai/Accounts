<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
Schema::dropIfExists('petty_cash_transactions');
Schema::dropIfExists('petty_cash_days');
Schema::dropIfExists('expense_categories');
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Tables dropped.\n";
