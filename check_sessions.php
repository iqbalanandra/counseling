<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$sessions = DB::table('sessions')->get(['user_id', 'ip_address', 'last_activity']);
foreach ($sessions as $s) {
    echo 'user_id: '.($s->user_id ?? 'null').' | ip: '.$s->ip_address.' | last: '.date('Y-m-d H:i:s', $s->last_activity).PHP_EOL;
}
