<?php

/**
 * AbstractPublicApi
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

use OpenAPIServer\Repository\EventRepository;
use OpenAPIServer\Repository\TicketRepository;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Exception;

/**
 * AbstractPublicApi Class Doc Comment
 *
 * @package OpenAPIServer\Api
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
class PublicApi extends AbstractPublicApi
{

    /**
     * @var EventRepository Repository of events
     */
    protected $eventRepo;

    /**
     * @var TicketRepository DB access layer for tickets
     */
    protected $ticketRepo;

    protected $siteConfig;

    /**
     * Route Controller constructor receives container
     *
     * @param EventRepository $container Slim app container instance
     */
    public function __construct(EventRepository $eventRepo, TicketRepository $ticketRepo)
    {
        $this->eventRepo = $eventRepo;
        $this->ticketRepo = $ticketRepo;
        $this->siteConfig = $GLOBALS['config'];

        if ($this->eventRepo == null) {
            throw new Exception("missing eventRepo");
        }
    }


    /**
     * GET getFilteredEvents
     * Summary: Filter the events list
     * Notes: Returns a list of events filtered by the query strings
     * Output-Formats: [application/xml, application/json]
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function getFilteredEvents(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // check that registration view and buy are enabled
        if(!$this->siteConfig['allow']['view_events']) {
            $response->getBody()->write('Viewing events is currently disabled');
            return $response->withStatus(401);
        }

        $queryParams = $request->getQueryParams();
        $search = (key_exists('search', $queryParams)) ? $queryParams['search'] : null;
        $day = (key_exists('day', $queryParams)) ? $queryParams['day'] : null;
        $category = (key_exists('category', $queryParams)) ? $queryParams['category'] : null;
        $ages = (key_exists('ages', $queryParams)) ? $queryParams['ages'] : null;
        $tags = (key_exists('tags', $queryParams)) ? $queryParams['tags'] : null;

        $idConvention = $this->siteConfig['gcs']['year'];
        $events = $this->eventRepo->findPublicEvents($idConvention, $search, $day, $category, $ages, $tags);

        if ($events == null || count($events)==0) {
            $response->getBody()->write('[]');
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        }

        // add ticket information to each event
        $eventIds = array_column($events, 'id');
        $ticketCounts = $this->ticketRepo->findCurrentTicketCountByEvents($eventIds);
        foreach ($events as $k => $event) {
            $id = $event->id;
            $fill = isset($ticketCounts[$id]) ? $ticketCounts[$id] : 0;
            $event->soldout = ($fill >= $event->maxplayers);
            if ($this->siteConfig['allow']['see_fill']) {
                $event->fill = (int)$fill;
            }
        }

        $response->getBody()->write( json_encode($events) );
        return $response->withStatus(200)->withHeader('Content-type', 'application/json');
    }

    /**
     * GET getPublicEventById
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
    public function getPublicEventById(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // check that registration view and buy are enabled
        if(!$this->siteConfig['allow']['view_events']) {
            $response->getBody()->write('Viewing events is currently disabled');
            return $response->withStatus(401);
        }

        $eventId = $args['eventId'];

        if ($this->eventRepo == null) {
            throw new Exception("missing repository");
        }

        try {
            $event = $this->eventRepo->findPublicEventById($eventId);
        } catch (\OutOfBoundsException $e) {
            $response->getBody()->write( "Not found" );
            return $response->withStatus(404);
        }

        // add ticket information to each event
        $eventIds = [$eventId];
        $ticketCounts = $this->ticketRepo->findCurrentTicketCountByEvents($eventIds);
        $fill = isset($ticketCounts[$eventId]) ? $ticketCounts[$eventId] : 0;
        $event->soldout = ($fill >= $event->maxplayers);
        if ($this->siteConfig['allow']['see_fill']) {
            $event->fill = $fill;
        }

        $response->getBody()->write( json_encode($event) );
        return $response->withStatus(200)->withHeader('Content-type', 'application/json');
    }
}
