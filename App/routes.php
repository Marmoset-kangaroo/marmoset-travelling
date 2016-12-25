<?php
$app->map(
	[
		'path' => '/',
		'controller' => [\App\Controllers\Test::class, 'home'],
	],
	'homePage'
);

$app->mapGroup(
	'test',
	[
		'testIndex' => [
			'path' => '/map',
			'controller' => [\App\Controllers\Test::class, 'map'],
		],

	]
);
