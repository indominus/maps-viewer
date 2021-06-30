<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

$response = (new \App\Bootstrap())->run();

http_response_code($response->getStatusCode());

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

foreach ($response->getHeaders() AS $key => $header) {
    header(sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue()));
}

echo  $response->getContent();
