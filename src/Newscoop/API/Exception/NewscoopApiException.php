<?php
/**
 * @package Newscoop\PHP-SDK
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\API\Exception;

/**
 * Thrown when an API call returns an exception.
 *
 */
class NewscoopApiException extends \Exception
{
  /**
   * The result from the API server that represents the exception information.
   */
  protected $result;

  /**
   * Make a new API Exception with the given result.
   *
   * @param array $result The result from the API server
   */
  public function __construct($result) {
    $this->result = $result = $result['errors'];


    $code = isset($result[0]['code']) ? $result[0]['code'] : 0;

    if (isset($result[0]['message'])) {
        $msg = $result[0]['message'];
    } else {
        $msg = 'Unknown Error. Check getResult()';
    }

    parent::__construct($msg, $code);
  }

  /**
   * Return the associated result object returned by the API server.
   *
   * @return array The result from the API server
   */
  public function getResult() {
      return $this->result;
  }

  /**
   * Returns the associated type for the error. This will default to
   * 'Exception' when a type is not available.
   *
   * @return string
   */
  public function getType() {
    if (isset($this->result[0]['type'])) {
        $error = $this->result[0]['type'];
        if (is_string($error)) {
            return $error;
        }
    }

    return 'NewscoopApiException';
  }
}