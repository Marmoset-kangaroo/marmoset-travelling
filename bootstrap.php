<?php
$loader = require __DIR__.'/vendor/autoload.php';
$loader->register();

$app = App\Core\Http::getInstance();

try {
	require_once __DIR__ . '/App/routes.php';

	$request = $app->getRequest();
	$response = $app->handle($request);

	$response->send();
} catch (Exception $e) {
	die(json_encode([
		'status' => 'Global error',
		'message' => 'Error:' . $e->getMessage(),
	]));
}
