<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RUTA
 * Sectores
 */

$app->group('/sectores', function () use ($app) {
    $app->map(['GET', 'POST'], '', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isGet()) {

                $sector = new Sector($this->logger);

                $res = $sector->listarSectores();
            } else
            if ($request->isPost()) {
                $bodyIn = [];

                $bodyIn = $request->getParsedBody();
                @$nuevoSector = $bodyIn['data']['sector'];
             
                $sector = new Sector($this->logger);

                $res = $sector->insertarSector($nuevoSector);
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
    $app->map(['PUT', 'DELETE'], '/{idSector}', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            if ($request->isDelete()) {
                if (is_numeric($args['idSector'])) {

                    $sector = new Sector($this->logger);

                    $res = $sector->eliminarSector($args['idSector']);
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
