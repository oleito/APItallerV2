<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization, Token");
header("Content-Type: application/json");


use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require '../vendor/autoload.php';

require_once './../src/classes/autoload_classes.php';

// Cambiar en prod
$config['displayErrorDetails'] = true;

$config['addContentLengthHeader'] = true;
set_time_limit(300);

$app = new \Slim\App(['settings' => $config]);

/**
 *  CONTENEDORES
 */

$container = $app->getContainer();

$container['logger'] = function ($c) {
    $logger = new \Monolog\Logger('REPORTES');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    $logger->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
    return $logger;
};

/**
 * APP
 */

$app->get('/', function (Request $request, Response $response, array $args) {

    $rp['data'] = 'soy un dato de ejemplo, no esperes mucho de mi.';

    // $this->logger->warning('Soy un warning - ', []);
    $pdo = new pdoMysql($this->logger);

    $pdo->conectar();

    return $response->withHeader('Content-type', 'application/json')
        ->withStatus(200)
        ->withJson($rp);

});

require_once './../src/rutas/autoload_rutas.php';

$app->run();
