<?php

/**
 * EventApi
 *
 * PHP version 7.1
 *
 * @package OpenAPIServer\Api
 * @author  GameConSuite team
 * @link    https://github.com/openapitools/openapi-generator
 */

namespace OpenAPIServer\Api;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Exception;

/**
 * EventApi Class Doc Comment
 *
 * @package OpenAPIServer\Api
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
class EventApi extends AbstractEventApi
{
    /**
     * @var NewADOConnection|null databasec connection
     */
    protected $db;

    /**
     * Route Controller constructor receives container
     *
     * @param ContainerInterface|null $container Slim app container instance
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $GLOBALS['app']->getContainer();
        $this->db = $this->container->get(\NewADOConnection::class);
        $this->config = $this->container->get('config');

        // check for misconfiguration
        if ($this->container == null) {
            throw new Exception("missing container");
        }
        if ($this->db == null) {
            throw new Exception("missing database");
        }
    }



    /**
     * POST addEvent
     * Summary: Add a new event to the convention
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function addEvent(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $body = $request->getParsedBody();
        $message = "How about implementing addEvent as a POST method in OpenAPIServer\Api\EventApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }

    /**
     * DELETE deleteEvent
     * Summary: Deletes an event
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function deleteEvent(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $headers = $request->getHeaders();
        $apiKey = $request->hasHeader('api_key') ? $headers['api_key'] : null;
        $eventId = $args['eventId'];
        $message = "How about implementing deleteEvent as a DELETE method in OpenAPIServer\Api\EventApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }

    /**
     * GET getEventById
     * Summary: Find event by ID
     * Notes: Returns a single event
     * Output-Formats: [application/xml, application/json]
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function getEventById(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $eventId = $args['eventId'];

        if ($this->db == null) {
            throw new Exception("missing database");
        }


        $publicFields = ['id_event', 'id_convention', 'id_gm', 's_number', 's_title', 's_game', 's_desc', 's_desc_web', 'i_minplayers', 'i_maxplayers', 'i_agerestriction', 'e_exper', 'e_complex', 'i_length', 'e_day', 'i_time', 'id_room', 's_table', 'i_cost'];

        $sql = 'select '.join(',', $publicFields).' from ucon_event where id_event=?';
        $result = $this->db->getAll($sql, [$eventId]);
        if (!is_array($result)) {
            $msg = 'SQL Error: '.$this->db->ErrorMsg();
            throw new Exception($msg);
        }


        // if (!$this->container->has('db')) {
        //     $message = "Database Service not found";
        //     throw new Exception($message);
        // }


        $response->getBody()->write( json_encode($result) );
        return $response->withStatus(200)->withHeader('Content-type', 'application/json');





        $message = "How about implementing getEventById as a GET method in OpenAPIServer\Api\EventApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }

    /**
     * PUT updateEvent
     * Summary: Update an existing event
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function updateEvent(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $body = $request->getParsedBody();
        $message = "How about implementing updateEvent as a PUT method in OpenAPIServer\Api\EventApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }

    /**
     * POST updateEventWithForm
     * Summary: Updates an event in the convention with form data
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function updateEventWithForm(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $eventId = $args['eventId'];
        $body = $request->getParsedBody();
        $name = (isset($body['name'])) ? $body['name'] : null;
        $status = (isset($body['status'])) ? $body['status'] : null;
        $message = "How about implementing updateEventWithForm as a POST method in OpenAPIServer\Api\EventApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }
}
