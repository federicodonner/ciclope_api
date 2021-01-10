<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';
require '../src/auxiliares/funciones.php';


$app = new \Slim\App;
// require '../src/middleware/authentication.php';

// Customer routes
require '../src/routes/siguiente.php';
require '../src/routes/registro.php';
require '../src/routes/cors.php';


$app->run();
