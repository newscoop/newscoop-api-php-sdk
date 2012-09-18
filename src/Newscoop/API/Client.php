<?php
/**
 * @package Newscoop\PHP-SDK
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\API;

use Buzz;
use Newscoop\API\Builder\ApiCallBuilder;
use Newscoop\API\Exception\NewscoopApiException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

if (!function_exists('curl_init')) {
    throw new Exception('Newscoop PHP-SDK needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Newscoop PHP-SDK needs the JSON PHP extension.');
}

/**
 * Provides access to the Newscoop REST API. This class provides
 * a majority of the functionality needed.
 */
class Client 
{

    /**
    * Version.
    */
    const VERSION = '1.0.0';

    /**
     * apiEndpoint used in request
     * @var string
     */
    private $apiEndpoint;

    /**
     * Browser for making API requests
     * @var Buzz\Browser
     */
    private $browser;

    /**
     * Event Dispatcher
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * Response from Buzz\Browser
     * @var string
     */
    private $response;

    /**
     * Final uri called by Buzz\Browser
     * @var string
     */
    private $uri;
    
    /**
     * Set uri
     * @param string $uri resource uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     * 
     * @return string resource uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
    * Initialize a Newscoop PHP-SDK.
    */
    public function __construct($apiEndpoint = 'http://newscoop.dev/api')
    {
        $this->setApiEndpoint($apiEndpoint);
        $this->browser = new Buzz\Browser(new Buzz\Client\Curl());
        $this->dispatcher = new EventDispatcher();
        
        // Create final uri called by Buzz\Browser
        $this->dispatcher->addListener('api.createUri', 
            function (GenericEvent $event) {
                $uri = $this->getApiEndpoint().$event->getArgument('path');
                $uri = $uri.'?'.http_build_query($event->getArgument('params'));

                $this->setUri($uri);
            }
        );
    }

    /**
     * Set Api Endpoint uri
     * @param string $endpoint Api Endpoint uri
     */
    public function setApiEndpoint($endpoint)
    {
        $this->apiEndpoint = $endpoint;

        return $this;
    }

    /**
     * Get Api Endpoint uri
     */
    public function getApiEndpoint()
    {
        return $this->apiEndpoint;
    }

    /**
     * Get ApiCallBuilder for resource
     * @param string $path resource path
     * @param array $params resource params
     * @return ApiCallBuilder ApiCallBuilder object
     */
    public function getResource($path, $params = array())
    {   
        return new ApiCallBuilder($this, $path, $params);
    }

    /**
     * Make request to resource
     * 
     * @return object Client
     */
    public function makeRequest($path, $params)
    {
        $this->dispatcher->dispatch('api.createUri', 
            new GenericEvent($this, array(
                'path' => $path,
                'params' => $params
            ))
        );

        $this->response = $this->browser->get($this->getUri());       

        $parsedResponse = json_decode($this->response->getContent(), true);
        if (array_key_exists('errors', $parsedResponse)) {
            throw new NewscoopApiException($parsedResponse);
        }

        return $this;
    }

    /**
     * Get content from api response
     * 
     * @return string content from response
     */
    public function getResult()
    {   
        return $this->response->getContent();
    }

    /**
     * Convert api response content to array
     * 
     * @return array content from response
     */
    public function toArray()
    {
        return json_decode($this->response->getContent(), true);
    }
}