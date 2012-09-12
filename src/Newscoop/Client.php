<?php
/**
 * @package Newscoop\PHP-SDK
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

use Buzz;
use Newscoop\Exception\NewscoopApiException;
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
class Client {

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
    * Initialize a Newscoop PHP-SDK.
    */
    public function __construct($apiEndpoint = 'http://newscoop.dev/api')
    {
        $this->setApiEndpoint($apiEndpoint);
        $this->browser = new Buzz\Browser(new Buzz\Client\Curl());
        $this->dispatcher = new EventDispatcher();
        
        // Create final uri called by Buzz\Browser
        $this->dispatcher->addListener('api.createUri', function (GenericEvent $event) {
            $uri = $this->getApiEndpoint() . $event->getArgument('path');
            $uri = $uri . '?' . http_build_query($event->getArgument('params'));

            $this->setUri($uri);
        });
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
     * Set uri
     * @param string $uri resource uri
     */
    private function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     * 
     * @return string resource uri
     */
    private function getUri()
    {
        return $this->uri;
    }

    /**
     * Call choosen resource
     * @param  string $path   resource path
     * @param  array  $params optional paramters
     * 
     * @return object         Client
     */
    public function api($path, $params)
    {
        $this->dispatcher->dispatch('api.createUri', new GenericEvent($this, array(
            'path' => $path,
            'params' => $params
        )));

        $this->response = $this->browser->get($this->getUri());

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