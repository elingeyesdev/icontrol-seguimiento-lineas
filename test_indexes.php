<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$res = DB::select("SHOW CREATE TABLE interno");
echo "interno:\n" . $res[0]->{'Create Table'} . "\n\n";

$res2 = DB::select("SHOW CREATE TABLE micro");
echo "micro:\n" . $res2[0]->{'Create Table'} . "\n";
