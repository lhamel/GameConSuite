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
 * This is the administrative API for GameConSuite.  You can find out more about Game Con Suite at  [https://gameconsuite.com](https://gameconsuite.com)
 * The version of the OpenAPI document: 1.0.0
 * Generated by: https://github.com/openapitools/openapi-generator.git
 */

/**
 * NOTE: This class is auto generated by the openapi generator program.
 * https://github.com/openapitools/openapi-generator
 * Do not edit the class manually.
 */
namespace OpenAPIServer\Api;

use OpenAPIServer\Repository\MemberRepository;

use PHPAuth\Auth as PHPAuth;

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
class AuthenticationApi extends AbstractAuthenticationApi
{

    /**
     * @var ContainerInterface|null Slim app container instance
     */
    protected $container;

    /**
     * @var PHPAuth|null Slim app container instance
     */
    protected $auth;

    /**
     * @var \Associates|null Slim app container instance
     */
    protected $associates;

    /**
     * @var MemberRepository|null Member Repository for retrieving envelops
     */
    protected $memberRepository;

    /**
     * Route Controller constructor receives container
     *
     * @param ContainerInterface|null $container Slim app container instance
     */
    public function __construct(PHPAuth $auth, \Associates $associates, MemberRepository $memberRepository, ContainerInterface $container = null)
    {
        $this->auth = $auth;
        $this->associates = $associates;
        $this->memberRepository = $memberRepository;
        $this->container = $container;
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

        if (isset($body) == 0) {
            $response->getBody()->write("Content-type header required");
            return $response->withStatus(400);
        }

        // if the user is currently logged in, this is an error
        if ($this->auth->isLogged()) {
            $hash = $this->auth->getCurrentSessionHash();
            $response->getBody()->write("Forbidden: logout before logging in $hash");
            return $response->withStatus(403);
        }

        // collect and test the credentials
        $email = $body['username'];
        $password = $body['password'];
        // $rememberMe = isset($body['rememberMe']);

        $return = $this->auth->login($email, $password, 1);
        if ($return['error']) {

            // generate login error message
            $errMsg = $return['message'];
            if ($errMsg == "Account has not yet been activated.") {
                // $smarty->assign('resendAction', 'resendactivation.php');
                $response->getBody()->write($errMsg);
                return $response->withStatus(403);

            }
            else if ($errMsg == "Email address / password are incorrect.") 
            {
                $errMsg .= "  Do you need to create your account?";
                $response->getBody()->write($errMsg);
                return $response->withStatus(401);
            }

            $response->getBody()->write($errMsg);
            return $response->withStatus(400);
        }

        // login had no error

        // set up the authentication token
        $hash = $return['hash'];

        // check to see if there are associated members
        $members = $this->associates->listAssociates();


        // TODO move the automated linking function
        // if (count($members)==0) {

        //     // automatically associate auth with members based on email address
        //     $uid = $this->auth->getCurrentUser()['uid'];
        //     $email = $this->auth->getCurrentUser()['email'];
        //     $sql = "insert into ucon_auth_member "
        //          . "(select $uid,id_member from ucon_member where s_email=?)";
        //     $succ = $db->execute($sql, array($email));
        //     if (!$succ) {
        //         $msg = "Sql Error ($sql): ". $db->ErrorMsg();
        //         throw new Exception($msg);
        //         // $response->getBody()->write($msg);
        //         // return $response->withStatus(500);
        //     }
        // }

        // TODO remove token from body
        $response = $response->withHeader('Authorization', "Bearer ".$hash);
        $response->getBody()->write("Login succeeded $hash");
        return $response->withStatus(200);
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

        // if the user is currently logged in, this is an error
        if ($this->auth->isLogged()) {
            $s = $this->auth->logout($this->auth->getCurrentSessionHash());
        }

        // return $response->withHeader(
        //     'Set-Cookie', 
        //     'Authentication=; HttpOnly; Secure; Path=/; Max-Age=0'
        // );

        $response->getBody()->write("Logout succeeded");
        return $response->withStatus(200);
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
        // if the user is currently logged in, this is an error
        if ($this->auth->isLogged()) {
            $hash = $this->auth->getCurrentSessionHash();
            $response->getBody()->write(json_encode($hash));
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        }

        $response->getBody()->write("login required");
        return $response->withStatus(403);
    }
}
