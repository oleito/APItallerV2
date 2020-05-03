<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RUTA
 * Ordenes
 */

$app->group('/ordenes', function () use ($app) {
    $app->map(['GET', 'POST'], '', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isGet()) {

                $orden = new Orden($this->logger);

                $res = $orden->listarOrdenes();
            } else
            if ($request->isPost()) {
                $bodyIn = [];

                $bodyIn = $request->getParsedBody();
                @$idOrden = $bodyIn['data']['orden'];
                @$vehiculo = $bodyIn['data']['vehiculo'];
                @$seguro = $bodyIn['data']['seguro'];
                @$fecha = $bodyIn['data']['fechaentrega'];
                @$repuestos = $bodyIn['data']['esperarepuestos'];
                @$observacion = $bodyIn['data']['observacion'];

                $orden = new Orden($this->logger);

                $res = $orden->insertarOrden($idOrden, $vehiculo, $seguro, $fecha, $repuestos, $observacion);
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
    $app->map(['PUT', 'DELETE'], '/{idOrden}', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isDelete()) {
                if (is_numeric($args['idOrden'])) {

                    $orden = new Orden($this->logger);

                    $res = $orden->eliminarOrden($args['idOrden']);
                }

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
    $app->map(['GET', 'POST'], '/{idOrden}/movimientos', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isGet()) {
                if (is_numeric($args['idOrden'])) {

                    $mov = new Movimiento($this->logger);

                    $res = $mov->listarMovimientos($args['idOrden']);
                } else {
                    $res = 404;
                }

            } else
            if ($request->isPost()) {
                $bodyIn = [];

                $bodyIn = $request->getParsedBody();
                @$orden = $args['idOrden'];
                @$fecha = $bodyIn['data']['fecha'];
                @$usuario = $bodyIn['data']['usuario'];
                @$sector = $bodyIn['data']['sector'];

                $movimiento = new Movimiento($this->logger);

                $res = $movimiento->insertarMovimiento($orden, $fecha, $usuario, $sector);
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
    $app->map(['PUT', 'DELETE'], '/{idOrden}/movimientos/{idMovimiento}', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isDelete()) {
                if (is_numeric($args['idMovimiento'])) {

                    $movimiento = new Movimiento($this->logger);

                    $res = $movimiento->eliminarMovimiento($args['idMovimiento'], $args['idOrden']);
                }

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
