<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RUTA
 * TIPOS
 */

$app->group('/tipos', function () use ($app) {
    $app->map(['GET', 'POST'], '', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isGet()) {

                $tipo = new Tipo($this->logger);

                $res = $tipo->listarTipos();
            } else
            if ($request->isPost()) {
                $bodyIn = [];

                $bodyIn = $request->getParsedBody();
                @$nuevaMarca = $bodyIn['data']['marca'];
                @$iniciales = $bodyIn['data']['iniciales'];

                $tipo = new Tipo($this->logger);

                $res = $tipo->insertarTipo($nuevaMarca, $iniciales);
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
    $app->map(['PUT', 'DELETE'], '/{idTipo}', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isDelete()) {
                if (is_numeric($args['idTipo'])) {

                    $tipo = new Tipo($this->logger);

                    $res = $tipo->eliminarTipo($args['idTipo']);
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
