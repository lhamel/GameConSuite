<?php

/**
 * GameConSuite Admin API
 *
 * PHP version 7.1
 *
 * @package OpenAPIServer\Api
 * @author  OpenAPI Generator team
 * @version 1.0.0
 * @link    https://github.com/openapitools/openapi-generator
 */

require_once __DIR__ . '/vendor/autoload.php';

use Psr\Container\ContainerInterface;

use PHPAuth\Auth as PHPAuth;

use OpenAPIServer\SlimRouter;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use OpenAPIServer\Mock\OpenApiDataMocker;

// $config = [];

// /**
//  * Token Middleware 1.x Options
//  * Options `header`, `regex`, `parameter`, `cookie`, `attribute`, `path`, `except`, `authenticator`
//  * are handled by SlimRouter class. These options are ignored by app and they omitted from current
//  * example.
//  * Ref: https://github.com/dyorg/slim-token-authentication/tree/1.x
//  */
// $config['tokenAuthenticationOptions'] = [
//     /**
//      * Tokens are essentially passwords. You should treat them as such and you should always
//      * use HTTPS. If the middleware detects insecure usage over HTTP it will return unathorized
//      * with a message Required HTTPS for token authentication. This rule is relaxed for requests
//      * on localhost. To allow insecure usage you must enable it manually by setting secure to
//      * false.
//      * Default: true
//      */
//     // 'secure' => true,

//     /**
//      * Alternatively you can list your development host to have relaxed security.
//      * Default: ['localhost', '127.0.0.1']
//      */
//     // 'relaxed' => ['localhost', '127.0.0.1'],

//     *
//      * By default on ocurred a fail on authentication, is sent a response on json format with a
//      * message (`Invalid Token` or `Not found Token`) and with the token (if found), with status
//      * `401 Unauthorized`. You can customize it by setting a callable function on error option.
//      * Default: null
     
//     // 'error' => null,
// ];

// /**
//  * Mocker Middleware options.
//  */
// $config['mockerOptions'] = [
//     // 'dataMocker' => new OpenApiDataMocker(),

//     // 'getMockResponseCallback' => function (ServerRequestInterface $request, array $responses) {
//     //     // check if client clearly asks for mocked response
//     //     if (
//     //         $request->hasHeader('X-OpenAPIServer-Mock')
//     //         && $request->getHeader('X-OpenAPIServer-Mock')[0] === 'ping'
//     //     ) {
//     //         if (array_key_exists('default', $responses)) {
//     //             return $responses['default'];
//     //         }

//     //         // return first response
//     //         return $responses[array_key_first($responses)];
//     //     }

//     //     return false;
//     // },

//     // 'afterCallback' => function ($request, $response) {
//     //     // mark mocked response to distinguish real and fake responses
//     //     return $response->withHeader('X-OpenAPIServer-Mock', 'pong');
//     // },
// ];


$dbSettings = [
    'host' => 'localhost',
    'dbname' => 'ucon_db',
    'user' => 'root',
    'pass' => ''
];


require_once __DIR__.'/../inc/db/db.php';
require_once __DIR__.'/../inc/auth.php';

$containerBuilder = new \DI\ContainerBuilder();

$containerBuilder->addDefinitions([
        'settings' => [
            'db'=>$dbSettings,
            // 'displayErrorDetails' => true, // Should be set to false in production
            // 'logger' => [
            //     'name' => 'my-app',
            //     'path' => 'php://stderr',
            //     'level' => Logger::DEBUG,
            // ],
        ],
        'config' => $GLOBALS['config'],
        \ADOConnection::class => function(ContainerInterface $c) {
            return $GLOBALS['db'];
        },
        PHPAuth::class=> $auth,
        \Associates::class=>$associates,
]);

//echo "<pre>".print_r($containerBuilder->build(), 1)."</pre>";
$container = $containerBuilder->build();
$router = new SlimRouter($container);
$app = $router->getSlimApp();
$app->setBasePath('/GameConSuite/api');


/**
 * Ensure that the API is not cached by attaching headers to all responses
 */
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader("Cache-Control", "no-cache, no-store, must-revalidate") // HTTP 1.1.
            ->withHeader("Pragma", "no-cache") // HTTP 1.0.
            ->withHeader("Expires", "0"); // Proxies.
            //->withHeader('Access-Control-Allow-Origin', 'http://mysite')
            //->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            //->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

/**
 * The routing middleware should be added before the ErrorMiddleware
 * Otherwise exceptions thrown from it will not be handled
 */
$app->addRoutingMiddleware();

/**
 * Add Error Handling Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.

 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$app->addErrorMiddleware(true, true, true);

$app->run();
