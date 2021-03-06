<?php

/**
 * AbstractAttendeeApi
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
use OpenAPIServer\Repository\EventRepository;
use OpenAPIServer\Repository\TicketRepository;
use OpenAPIServer\Model\CartItem;

use PHPAuth\Auth as PHPAuth;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Exception;

/**
 * AbstractAttendeeApi Class Doc Comment
 *
 * @package OpenAPIServer\Api
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
class AttendeeApi extends AbstractAttendeeApi
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
     * @var EventRepository|null Event Repository for retrieving ticket info
     */
    protected $eventRepo;

    /**
     * @var TicketRepository|null Ticket Repository for retrieving tickets
     */
    protected $ticketRepo;

    protected $siteConfig;

    /**
     * Route Controller constructor receives container
     *
     * @param ContainerInterface|null $container Slim app container instance
     */
    public function __construct(PHPAuth $auth, \Associates $associates, MemberRepository $memberRepository, EventRepository $eventRepo, TicketRepository $ticketRepo, ContainerInterface $container = null)
    {
        $this->auth = $auth;
        $this->associates = $associates;
        $this->memberRepository = $memberRepository;
        $this->eventRepo = $eventRepo;
        $this->ticketRepo = $ticketRepo;
        $this->container = $container;
        $this->siteConfig = $GLOBALS['config'];
    }


    /**
     * POST createUserEnvelope
     * Summary: Create envelope
     * Notes: create an envelope for current user
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function createUserEnvelope(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = $this->auth->getCurrentUser()['uid'];

        // TODO check the user's authorizations
        $members = $associates->listAssociates($uid);


        $response->getBody()->write(json_encode($members));
        return $response->withStatus(200);



        $message = "How about implementing createUserEnvelope as a POST method in OpenAPIServer\Api\AttendeeApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }

    /**
     * GET getUserEnvelopes
     * Summary: Get list of attendees envelopes
     * Notes: Get list of attendees envelopes
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function getUserEnvelopes(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = $this->auth->getCurrentUser()['uid'];

        // TODO check the user's authorizations
        $members = $this->associates->listAssociates($userId);

        // TODO pull the members from the Member repository

        $response->getBody()->write(json_encode($members));
        return $response->withStatus(200);


        $message = "How about implementing getUserEnvelopes as a GET method in OpenAPIServer\Api\AttendeeApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }


    /**
     * GET getScheduleByMember
     * Summary: Get the complete schedule for the specified member
     * Output-Formats: [application/json]
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function getScheduleByMember(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $memberId = $args['memberId'];

        // check that the user is logged in
        if (!$this->auth->isLogged()) {
            $response->getBody()->write('Unauthorized');
            return $response->withStatus(401);
        }

        // test the member is listed in the associates for the logged in user
        $userId = $this->auth->getCurrentUser()['uid'];
        $members = $this->associates->listAssociates($userId);
        // echo print_r($members, 1)."\n\n";
        if (!isset($members[$memberId])) {
            $response->getBody()->write('Unauthorized');
            return $response->withStatus(401);
        }

        // find events that belong to the gamemaster
        // try {
        $events = $this->eventRepo->findCurrentPrivateEventsByGM($memberId);
        // } catch (\OutOfBoundsException $e) {
        //     $response->getBody()->write( "Not found" );
        //     return $response->withStatus(404);
        // }

        // add ticket information to each event
        // $eventIds = array_column($events, 'id');
        // $ticketCounts = $this->ticketRepo->findCurrentTicketCountByEvents($eventIds);
        // foreach ($events as $k => $event) {
        //     $id = $event->id;
        //     $event->prereg = isset($ticketCounts[$id]) ? $ticketCounts[$id] : 0;
        // }

        // find games where I have a ticket
        $ticketItems = $this->ticketRepo->findMemberTickets($memberId);
        $eventIds = array_column($ticketItems, 'subtype');
        $indexedEvents = $this->eventRepo->findIndexedEvents($eventIds);

        // merge the GM events and tickets
        $schedule = [];
        foreach ($events as $e) {
            $schedule[] = ['event'=>$e, 'ticket'=>null ];
        }
        foreach ($ticketItems as $t) {
            $e = $indexedEvents[$t['subtype']];
            $schedule[] = [ 'event'=>$e, 'ticket'=>$t ];
        }

        // sort by day an time
        usort($schedule, array($this, 'sortScheduleByTime'));

        $response->getBody()->write( json_encode($schedule) );
        return $response->withStatus(200)->withHeader('Content-type', 'application/json');
    }


    /**
     * GET getUserTickets
     * Summary: Get envelopes with tickets
     * Notes: Get list of attendee envelopes and all the tickets for each
     * Output-Formats: [application/json]
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function getUserTickets(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        // check that the user is logged in
        if (!$this->auth->isLogged()) {
            $response->getBody()->write('Unauthorized');
            return $response->withStatus(401);
        }

        // get the listed associates for the logged in user
        $userId = $this->auth->getCurrentUser()['uid'];
        $associates = $this->associates->listAssociates();

        if (count($associates)==0) {
            $response->getBody()->write('[]');
            return $response->withStatus(200);
        }

        // get all the member associated with this login account
        $memberIds = array_keys($associates);
        $members = $this->memberRepository->findPrivateMembersByIds($memberIds);

        // Get the tickets for each member
        // TODO improve performance by getting all tickets at once
        foreach ($members as $k=>$m) {
            $memberId = $m->id;
            $ticketItems = $this->ticketRepo->findMemberTickets($memberId);
            $members[$k]->tickets = $ticketItems;
        }

        $response->getBody()->write(json_encode($members));
        return $response->withStatus(200)->withHeader('Content-type', 'application/json');
    }


    /**
     * GET getCartByMember
     * Summary: Get the cart for the specified member
     * Output-Formats: [application/json]
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function getCartByMember(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $memberId = (int) $args['memberId'];

        // check that the user is logged in
        if (!$this->auth->isLogged()) {
            $response->getBody()->write('Unauthorized - user must log in');
            return $response->withStatus(401);
        }

        // test the member is listed in the associates for the logged in user
        $userId = $this->auth->getCurrentUser()['uid'];
        $members = $this->associates->listAssociates($userId);
        // echo print_r($members, 1)."\n\n";
        if (!isset($members[$memberId])) {
            $response->getBody()->write('Unauthorized');
            return $response->withStatus(401);
        }

        $cartItems = $this->ticketRepo->findCartItemsByMember($memberId);

        // find games where member has a ticket
        $ticketItems = array_filter($cartItems, function($v){ return $v['type']=='Ticket'; } );
        $eventIds = array_column($ticketItems, 'subtype');
        $indexedEvents = $this->eventRepo->findIndexedEvents($eventIds);

        // add event info to tickets in the cart
        $balance = 0;
        foreach ($cartItems as $k => $v)
        {
            if ($v['type']=='Ticket')
            {
                $eventId = (int) $v['subtype'];
                $event = $indexedEvents[$eventId];
                $cartItems[$k]['event'] = $event;
            }
            $balance += $v['quantity']*$v['price'];
        }

        $member = $this->memberRepository->findPublicMemberById($memberId);

        $cart = [
            'member'=>$member,
            'pendingPaymentAmount'=>$this->ticketRepo->getPendingPaymentAmount($memberId),
            'balance'=>$balance,
            'items'=>$cartItems,
        ];

        // TODO someday should we limit the event info to only what it needed?  this call currently returns extra fields

        $response->getBody()->write(json_encode($cart));
        return $response->withStatus(200)->withHeader('Content-type', 'application/json');
    }


    /**
     * PUT addItemToCart
     * Summary: Add an item to the cart
     * Output-Formats: [application/json]
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function addItemToCart(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $memberId = $args['memberId'];
        $body = $request->getParsedBody();

        // check that registration view and buy are enabled
        if(!$this->siteConfig['allow']['view_events'] || !$this->siteConfig['allow']['buy_events']) {
            $response->getBody()->write('Modifying registration is currently disabled');
            return $response->withStatus(401);
        }

        // check that the user is logged in
        if (!$this->auth->isLogged()) {
            $response->getBody()->write('Unauthorized - user must log in');
            return $response->withStatus(401);
        }

        // test the member is listed in the associates for the logged in user
        $userId = $this->auth->getCurrentUser()['uid'];
        $members = $this->associates->listAssociates($userId);
        // echo print_r($members, 1)."\n\n";
        if (!isset($members[$memberId])) {
            $response->getBody()->write('Unauthorized');
            return $response->withStatus(401);
        }

        // test that type, subtype, and quantity are filled in
        if (!isset($body['type']) || !isset($body['subtype']) || !isset($body['quantity'])) {
            $response->getBody()->write('Malformed request');
            return $response->withStatus(400);
        }

// TODO if id is provided, use that value for the id?

        $type = $body['type'];
        $subtype = $body['subtype'];
        $quantity = $body['quantity'];

        switch($type) {
            case 'Badge':

                // check that special is filled in
                if (!isset($body['special'])) {
                    $response->getBody()->write('Malformed request: special required for Badge');
                    return $response->withStatus(400);
                }
                // check that quantity is 1
                if ($quantity != 1) {
                    $response->getBody()->write('Malformed request: quantity must be 1 for Badge');
                    return $response->withStatus(400);
                }
                $special = $body['special'];

                // all conditions are met, add the ticket
                $cartItem = new CartItem();
                $cartItem->memberId = $memberId;
                $cartItem->type = $type;
                $cartItem->subtype = $subtype;
                $cartItem->quantity = 1;
                $cartItem->special = $special;

                // find the badge cost
                $cartItem->price  = $this->ticketRepo->lookupCartItemUnitPrice($cartItem);

                $newCartItem = $this->ticketRepo->addVerifiedCartItem($cartItem);

                $response->getBody()->write(json_encode($newCartItem));
                return $response->withStatus(200);

                break;
            case 'Ticket':

                // check if this player already has a ticket
                $tickets = $this->ticketRepo->findMemberTickets($memberId);
                $ticketIds = array_column($tickets, 'subtype');
                $has = in_array($subtype, $ticketIds);

                if ($has) {
                    $response->getBody()->write('Ticket already added');
                    return $response->withStatus(409);
                }

                // check that ticket is available
                $event = $this->eventRepo->findById((int)$subtype);
                $counts = $this->ticketRepo->findCurrentTicketCountByEvents([(int)$subtype]);
                $count = isset($counts[(int)$subtype]) ? $counts[(int)$subtype] : 0;

                $ticketsLeft = $event->maxplayers - $count;
                if ($ticketsLeft <= 0) {
                    $response->getBody()->write('Event sold out');
                    return $response->withStatus(409);
                }
                if ($ticketsLeft < $quantity) {
                    $response->getBody()->write("Not enough tickets: $quantity wanted, $ticketsLeft left");
                    return $response->withStatus(409);
                }

                // all conditions are met, add the ticket
                $cartItem = new CartItem();
                $cartItem->memberId = $memberId;
                $cartItem->type = $type;
                $cartItem->subtype = $subtype;
                $cartItem->quantity = $quantity;
                $cartItem->price = $event->cost;
                $this->ticketRepo->addVerifiedCartItem($cartItem);

                $response->getBody()->write(json_encode($cartItem));
                return $response->withStatus(200);
                break;
            default:
                $response->getBody()->write($message);
                return $response->withStatus(501);
        }


        $message = "Unhandled case in addItemToCart method in OpenAPIServer\Api\AttendeeApi class";
        throw new Exception($message);

        // $response->getBody()->write($message);
        // return $response->withStatus(501);
    }



    /**
     * DELETE removeItemFromCart
     * Summary: Remove item from cart
     * Output-Formats: [application/json]
     *
     * @param ServerRequestInterface $request  Request
     * @param ResponseInterface      $response Response
     * @param array|null             $args     Path arguments
     *
     * @return ResponseInterface
     * @throws Exception to force implementation class to override this method
     */
    public function removeItemFromCart(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // check that registration view and buy are enabled
        if(!$this->siteConfig['allow']['view_events'] || !$this->siteConfig['allow']['buy_events']) {
            $response->getBody()->write('Modifying registration is currently disabled');
            return $response->withStatus(401);
        }

        $memberId = $args['memberId'];
        $itemId = $args['itemId'];

        // check that registration view and buy are enabled
        if(!$this->siteConfig['allow']['view_events'] || !$this->siteConfig['allow']['buy_events']) {
            $response->getBody()->write('Buying events is disabled');
            return $response->withStatus(403);
        }

        // check that the user is logged in
        if (!$this->auth->isLogged()) {
            $response->getBody()->write('Unauthorized - user must log in');
            return $response->withStatus(401);
        }

        // test the member is listed in the associates for the logged in user
        $userId = $this->auth->getCurrentUser()['uid'];
        $members = $this->associates->listAssociates($userId);
        // echo print_r($members, 1)."\n\n";
        if (!isset($members[$memberId])) {
            $response->getBody()->write('Unauthorized');
            return $response->withStatus(401);
        }

        // TODO does the member have this cart item?


        $ok = $this->ticketRepo->removeVerifiedCartItem($memberId, $itemId);
        if ($ok) {
            $response->getBody()->write('Item removed');
            return $response->withStatus(200);
        }

        $response->getBody()->write('Item not found');
        return $response->withStatus(403);


        $message = "How about implementing removeItemFromCart as a DELETE method in OpenAPIServer\Api\AttendeeApi class?";
        throw new Exception($message);

        $response->getBody()->write($message);
        return $response->withStatus(501);
    }




    private function sortScheduleByTime($a, $b) {
        $a1 = $a['event'];
        $b1 = $b['event'];
        // TODO fix to work with Mon-Thurs
        if ($a1->day != $b1->day) {
            return ($a1->day > $b1->day) ? 1 : -1;
        }
        if ($a1->time != $b1->time) {
            return ($a1->time > $b1->time) ? 1 : -1;
        }
        return ($a1->id > $b1->id) ? 1 : -1;
    }
}
