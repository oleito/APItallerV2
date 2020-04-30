<?php

/**
 * RUTA
 * LOGIN
 */

$app->group('/marcas', function () use ($app) {

    /** GET */
    $app->get('', function ($request, $response) {
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
    $app->POST('', function ($request, $response) {
        if ($request->getAttribute('isLoggedIn') === 'true') {
            $rp['token'] = $request->getAttribute('newToken');

            $bodyIn = [];

            $bodyIn = $request->getParsedBody();
            @$nuevaMarca = $bodyIn['data']['marca'];
            @$iniciales = $bodyIn['data']['iniciales'];

            $marca = new Marca($this->logger);

            $res = $marca->insertarMarca($nuevaMarca,$iniciales);

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
