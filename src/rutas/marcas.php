<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RUTA
 * LOGIN
 */

$app->group('/marcas', function () use ($app) {

    /** GET */
    $app->get('', function (Request $request, Response $response) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            $marca = new Marca($this->logger);

            $res = $marca->listarMarcas();

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

    /** POST */
    $app->post('', function (Request $request, Response $response) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            $bodyIn = [];

            $bodyIn = $request->getParsedBody();
            @$nuevaMarca = $bodyIn['data']['marca'];
            @$iniciales = $bodyIn['data']['iniciales'];

            $marca = new Marca($this->logger);

            $res = $marca->insertarMarca($nuevaMarca, $iniciales);

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

    /** DELETE */
    $app->delete('/{idMarca}', function (Request $request, Response $response, array $args) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            $res = 400;
            if (is_numeric($args['idMarca'])) {
                $marca = new Marca($this->logger);

                $res = $marca->eliminarMarca($args['idMarca']);
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
