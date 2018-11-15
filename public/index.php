<?php

require '../vendor/autoload.php';

require __DIR__ . '/../src/config.php';


$app = new \Slim\App(['settings' => $config]);

// Setup Dependencies
require __DIR__ . '/../src/dependencies.php';

// Setup Middleware
require __DIR__ . '/../src/middleware.php';

// Setup Routes
require __DIR__ . '/../src/routes.php';

$app->run();
