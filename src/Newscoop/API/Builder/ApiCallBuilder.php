<?php
/**
 * @package Newscoop\PHP-SDK
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\API\Builder;

use Newscoop\API\Client;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Provide helper methods for request
 */
class ApiCallBuilder {

    /**
     * Client instance
     * @var Client
     */
    private $client;

    /**
     * Contruct ApiCallBuilder object
     * @param Client $client Client instance
     */
    public function __construct(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Helper method - set items_per_page
     * @param integer $number Number of items per page
     */
    public function setItemsPerPage($number)
    {
        $this->client->addParam('items_per_page', $number);

        return $this;
    }

    /**
     * Helper method - set page number
     * @param integer $number requested page number
     */
    public function setPage($number)
    {
        $this->client->addParam('page', $number);

        return $this;
    }

    /**
     * Helper method - set order
     * @param array $order items order
     */
    public function setOrder($order = array())
    {
        $this->client->addParam('order', $order);

        return $this;
    }

    /**
     * Helper method - set requested fields
     * @param array $fields requested fields
     */
    public function setFields($fields = array())
    {
        $this->client->addParam('fields', implode(',', $fields));

        return $this;
    }

    /**
     * Make request to resource
     * 
     * @return object Client
     */
    public function makeRequest()
    {
        return $this->client->makeRequest();
    }
}