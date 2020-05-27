<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RUTA
 * MARCAS
 */

$app->group('/marcas', function () use ($app) {
    $app->map(['GET', 'POST'], '', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isGet()) {

                $marca = new Marca($this->logger);

                $res = $marca->listarMarcas();
            } else
            if ($request->isPost()) {
                $bodyIn = [];

                $bodyIn = $request->getParsedBody();
                @$nuevaMarca = $bodyIn['data']['marca'];
                @$iniciales = $bodyIn['data']['iniciales'];

                $marca = new Marca($this->logger);

                $res = $marca->insertarMarca($nuevaMarca, $iniciales);
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
    $app->map(['PUT', 'DELETE'], '/{idMarca}', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isDelete()) {
                if (is_numeric($args['idMarca'])) {

                    $marca = new Marca($this->logger);

                    $res = $marca->eliminarMarca($args['idMarca']);
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

    $app->map(['GET', 'POST'], '/{idMarca}/modelos', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isGet()) {

                $modelo = new Modelo($this->logger);

                $res = $modelo->listarModelos($args['idMarca']);
            } else
            if ($request->isPost()) {
                $bodyIn = [];

                $bodyIn = $request->getParsedBody();
                @$nuevaModelo = $bodyIn['data']['modelo'];
                @$marca = $args['idMarca'];
                @$tipo = $bodyIn['data']['tipo'];

                $modelo = new Modelo($this->logger);

                $res = $modelo->insertarModelo($nuevaModelo, $marca, $tipo);
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
    $app->map(['PUT', 'DELETE'], '/{idMarca}/modelos/{idModelo}', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isDelete()) {
                if (is_numeric($args['idModelo']
                    && is_numeric($args['idMarca']))) {

                    $modelo = new Modelo($this->logger);

                    $res = $modelo->eliminarModelo($args['idMarca'], $args['idModelo']);
                }

            } else if ($request->isPut()) {
                if (is_numeric($args['idModelo'])
                    && is_numeric($args['idMarca'])) {

                    $bodyIn = $request->getParsedBody();
                    @$marca = $args['idMarca'];
                    @$tipo = $bodyIn['Tipo'];

                    $modelo = new Modelo($this->logger);

                    $res = $modelo->actualizarModelo($args['idMarca'], $args['idModelo'], $tipo);
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
