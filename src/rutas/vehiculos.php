<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RUTA
 * Vehiculos
 */

$app->group('/vehiculos', function () use ($app) {
    $app->map(['GET', 'POST'], '', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isGet()) {

                $vehiculo = new Vehiculo($this->logger);

                $res = $vehiculo->listarVehiculos();
            } else
            if ($request->isPost()) {
                $bodyIn = [];

                $bodyIn = $request->getParsedBody();
                @$modelo = $bodyIn['data']['modelo'];
                @$patente = $bodyIn['data']['patente'];
                @$vin = $bodyIn['data']['vin'];
                @$color = $bodyIn['data']['color'];

                $vehiculo = new Vehiculo($this->logger);

                $res = $vehiculo->insertarVehiculo($modelo, $patente, $vin, $color);
            } else {
                $res = 405;
            }

            if (is_numeric($res)) {
                return $response->withHeader('Content-type', 'application/json')
                    ->withStatus($res)
                    ->withJson(null);
            } else {
                $rp['data'] = $res;
                return $response->withHeader('Content-type', 'application/json')
                    ->withStatus(200)
                    ->withJson($rp);
            }

        }
        return $response->withHeader('Content-type', 'application/json')
            ->withStatus(401)
            ->withJson(null);
    });
    $app->map(['PUT', 'DELETE'], '/{idVehiculo}', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isPut() && is_numeric($args['idVehiculo'])) {

                $vehiculo = new Vehiculo($this->logger);

                $bodyIn = $request->getParsedBody();
                if (array_key_exists('idModelo', $bodyIn['data'])) {
                    $res = $vehiculo->actualizarModeloVehiculo($args['idVehiculo'], $bodyIn['data']['idModelo']);
                } else {

                    @$patente = $bodyIn['data']['patente'];
                    @$vin = $bodyIn['data']['vin'];
                    @$color = $bodyIn['data']['color'];

                    $res = $vehiculo->actualizarDatosVehiculo($args['idVehiculo'], $patente, $vin, $color);
                }

            } else if ($request->isDelete() && is_numeric($args['idVehiculo'])) {

                $vehiculo = new Vehiculo($this->logger);

                $res = $vehiculo->eliminarVehiculo($args['idVehiculo']);

            } else {
                $res = 404;
            }

            if (is_numeric($res)) {
                return $response->withHeader('Content-type', 'application/json')
                    ->withStatus($res)
                    ->withJson(null);
            } else {
                $rp['data'] = $res;
                return $response->withHeader('Content-type', 'application/json')
                    ->withStatus(200)
                    ->withJson($rp);
            }

        }
        return $response->withHeader('Content-type', 'application/json')
            ->withStatus(401)
            ->withJson(null);
    });
})->add($guardMiddleware);
