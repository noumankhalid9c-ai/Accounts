<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = DB::select('SHOW TABLES LIKE "%expense%"');
print_r($tables);

$tables2 = DB::select('SHOW TABLES LIKE "%petty%"');
print_r($tables2);
