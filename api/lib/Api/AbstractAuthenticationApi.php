<?php

/**
 * AbstractAuthenticationApi
 *
 * PHP version 7.1
 *
 * @package OpenAPIServer\Api
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */

/**
 * GameConSuite Admin API
 *
 * This is the administrative API for GameConSuite.  You can find out more about Game Con Suite at  [https://gameconsuite.org](https://gameconsuite.org)
 * The version of the OpenAPI document: 1.0.0
 * Generated by: https://github.com/openapitools/openapi-generator.git
 */

/**
 * NOTE: This class is auto generated by the openapi generator program.
 * https://github.com/openapitools/openapi-generator
 * Do not edit the class manually.
 */
namespace OpenAPIServer\Api;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Exception;

/**
 * AbstractAuthenticationApi Class Doc Comment
 *
 * @package OpenAPIServer\Api
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
abstract class AbstractAuthenticationApi
{

    /**
     * @var ContainerInterface|null Slim app container instance
     */
    protected $container;

    /**
     * Route Controller constructor receives container
     *
     * @param ContainerInterface|null $container Slim app container instance
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    /**
     * GET getToken
     * Summary: if logged in, get the auth token
     * Output-Formats: [application/json]
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function getToken(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $message = "How about implementing getToken as a GET method in OpenAPIServer\Api\AuthenticationApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }

    /**
     * PUT login
     * Summary: Login with username and password
     * Notes: Begin authentication session
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $body = $request->getParsedBody();
        $message = "How about implementing login as a PUT method in OpenAPIServer\Api\AuthenticationApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }

    /**
     * PUT logout
     * Summary: Log out
     * Notes: End authentication session
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function logout(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $message = "How about implementing logout as a PUT method in OpenAPIServer\Api\AuthenticationApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }
}
