<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';
require '../src/auxiliares/funciones.php';

$app = new \Slim\App;
require '../src/middleware/authentication.php';

$container = $app->getContainer();
$container['upload_directory'] = __DIR__ . '/imagenes';

// Customer routes
require '../src/routes/oauth.php';
require '../src/routes/ciclope_juego.php';
require '../src/routes/ciclope_activar.php';
require '../src/routes/ciclope_registro.php';
require '../src/routes/ciclope_actividad.php';
require '../src/routes/usuario.php';
require '../src/routes/cors.php';


$app->run();
