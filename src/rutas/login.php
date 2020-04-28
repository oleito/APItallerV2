<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RUTA
 * LOGIN
 */

$app->post('/login', function (Request $request, Response $response, array $args) {

    $bodyIn = [];

    $bodyIn = $request->getParsedBody();
    @$userName = $bodyIn['data']['user_username'];
    @$userPassword = $bodyIn['data']['user_password'];

    $usuario = new usuario($this->logger);

    if ($usuario->login($userName, $userPassword)) {

        $usuario = $usuario->getUsuario();

        $token = new token;
        $rp['token'] = $token->setToken($usuario);

        $rp['data'] = '';

        return $response->withHeader('Content-type', 'application/json')
            ->withStatus(200)
            ->withJson($rp);
    } else {
        return $response->withHeader('Content-type', 'application/json')
            ->withStatus(401)
            ->withJson(null);
    }

});
