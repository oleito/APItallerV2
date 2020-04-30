<?php

/**
 * RUTA
 * LOGIN
 */

$app->group('/marcas', function () use ($app) {
    $app->get('', function ($request, $response) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            $marca = new Marca($this->logger);

            $res = $marca->listarMarcas();
    

            if ($res === 400) {
                return $response->withHeader('Content-type', 'application/json')
                    ->withStatus(400)
                    ->withJson(null);
            } else if ($res === 404) {
                return $response->withHeader('Content-type', 'application/json')
                    ->withStatus(404)
                    ->withJson(null);
            } else if ($res === 500) {
                return $response->withHeader('Content-type', 'application/json')
                    ->withStatus(500)
                    ->withJson(null);
            }

            $rp['data'] = $res;
            return $response->withHeader('Content-type', 'application/json')
                ->withStatus(200)
                ->withJson($rp);
        }
        return $response->withHeader('Content-type', 'application/json')
            ->withStatus(401)
            ->withJson(null);
    });

})->add($guardMiddleware);
