<?php

require realpath(__DIR__.'/../vendor/autoload.php');
$app = require_once realpath(__DIR__.'/../bootstrap/app.php');

$app->make('Illuminate\Contracts\Http\Kernel')
    ->handle(Illuminate\Http\Request::capture());

$isAuthorized = Auth::check();

print_r($isAuthorized ? 'yes' : 'no');

?>
