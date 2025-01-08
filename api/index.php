<?php  

require __DIR__ . '/../vendor/autoload.php';

// Boot Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Handle the incoming request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Send the response back to the browser
$response->send();

$kernel->terminate($request, $response);