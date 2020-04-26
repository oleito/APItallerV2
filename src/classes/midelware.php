<?php 
/**
 *  MIDDLEWARE
 */
$guardMiddleware = function ($request, $response, $next) {

    $token = new token();
    if ($request->hasHeader('token') && $token->checkToken($this->request->getHeaderLine('token'))) {

        $request = $request->withAttribute('isLoggedIn', 'true');
        $request = $request->withAttribute('newToken', $token->updateToken($this->request->getHeaderLine('token')));

    } else {
        $request = $request->withAttribute('isLoggedIn', 'false');
    }

    $tokenIn = $this->request->getHeaderLine('token');

    //**************** *************/
    //   before
    //**************** *************/
    $response = $next($request, $response);

    /**************** *************/
    // $response->getBody()->write('<br> AFTER 1');
    /**************** *************/

    return $response;
};
