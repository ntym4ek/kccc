<?php

/**
 * @file
 * Contains base API.
 */

/**
 * Base class for API implementations.
 */
abstract class DaDataApiBase {

  /**
   * The key to access the API.
   *
   * @var string
   */
  protected $apiKey;

  /**
   * Secret key for cleaner API.
   *
   * @var string
   */
  protected $secret;

  /**
   * The API request timeout.
   *
   * @var float
   */
  protected $timeout;

  /**
   * DaDataApiBase constructor.
   *
   * @param string|null $api_key
   *   The key to access the API.
   * @param string|null $secret
   *   Secret key for cleaner API.
   * @param float|null $timeout
   *   The API request timeout.
   */
  public function __construct($api_key = NULL, $secret = NULL, $timeout = NULL) {
    if ($api_key === NULL) {
      $api_key = variable_get('dadata_api_key', '');
    }
    if ($secret === NULL) {
      $secret = variable_get('dadata_api_secret', '');
    }
    if ($timeout === NULL) {
      $timeout = variable_get('dadata_api_timeout', 30);
    }

    $this->apiKey = $api_key;
    $this->secret = $secret;
    $this->timeout = $timeout;
  }

  /**
   * Gets the base URL of the API.
   *
   * @return string
   *   The base URL of the API.
   */
  abstract protected function getBaseUrl();

  /**
   * Gets the URL of the specified request.
   *
   * @param string|null $path
   *   Request path relative to base URL.
   * @param array $query
   *   An array of query key/value-pairs.
   *
   * @return string
   *   The URL of the specified request.
   */
  protected function getRequestUrl($path = NULL, array $query = array()) {
    $url = $this->getBaseUrl();

    if (!empty($path)) {
      $url .= '/' . $path;
    }
    if (!empty($query)) {
      $url .= '?' . http_build_query($query);
    }
    return $url;
  }

  /**
   * Gets the default request headers.
   *
   * @return array
   *   The default request headers as name/value pairs.
   */
  protected function getRequestHeaders() {
    return array(
      'Accept' => 'application/json',
      'Authorization' => 'Token ' . $this->apiKey,
    );
  }

  /**
   * Sends a request to the API.
   *
   * @param string $url
   *   The URL of the request.
   * @param array $options
   *   (optional) An array that can have one or more of the following elements:
   *   - headers: An array containing request headers as name/value pairs.
   *   - method: The request method ('GET' or 'POST'). Defaults to 'GET'.
   *   - secret: Defaults to FALSE. Whether to add a header with a secret key.
   *   - data: A string containing the request body (for POST requests).
   *   - timeout: Float representing the maximum number of seconds the API
   *     request may take.
   *
   * @return mixed|null
   *   Response data or NULL on failure.
   */
  protected function sendRequest($url, array $options = array()) {
    // Merge the default options.
    $options += array(
      'headers' => array(),
      'method' => 'GET',
      'secret' => FALSE,
      'data' => NULL,
      'timeout' => $this->timeout,
    );

    // Merge the default headers.
    $options['headers'] += $this->getRequestHeaders();

    // Add header with secret if necessary.
    if ($options['secret']) {
      $options['headers'] += array(
        'X-Secret' => $this->secret,
      );
    }

    // Add header for POST request.
    if ($options['method'] === 'POST') {
      $options['headers'] += array(
        'Content-Type' => 'application/json',
      );
    }
    // Only GET and POST requests are allowed.
    elseif ($options['method'] !== 'GET') {
      $options['method'] = 'GET';
    }

    $response = drupal_http_request($url, $options);
    if (empty($response->error) && $response->code >= 200 && $response->code < 300) {
      $data = drupal_json_decode($response->data);

      if (json_last_error() === JSON_ERROR_NONE) {
        return $data;
      }
    }
    return NULL;
  }

}

/**
 * Provides base API with information methods.
 */
class DaDataApi extends DaDataApiBase {

  /**
   * {@inheritdoc}
   */
  protected function getBaseUrl() {
    return 'https://dadata.ru/api/v2';
  }

  /**
   * Gets the information about API version.
   *
   * @return array|null
   *   The information about API version or NULL on failure.
   */
  public function getVersion() {
    static $result;

    if (!isset($result)) {
      $url = $this->getRequestUrl('version');
      $result = $this->sendRequest($url);
    }
    return $result;
  }

  /**
   * Gets the current balance.
   *
   * @return array|null
   *   The current balance or NULL on failure.
   */
  public function getBalance() {
    static $result;

    if (!isset($result)) {
      $url = $this->getRequestUrl('profile/balance');
      $result = $this->sendRequest($url, array('secret' => TRUE));
    }
    return $result;
  }

  /**
   * Gets the usage statistics for the specified day.
   *
   * @param string $date
   *   The date (in format 'Y-m-d') to return statistics. The default is today.
   *
   * @return array|null
   *   The usage statistics or NULL on failure.
   */
  public function getStat($date = '') {
    static $result = array();

    if (!isset($result[$date])) {
      $url = $this->getRequestUrl('stat/daily', array('date' => $date));
      $result[$date] = $this->sendRequest($url, array('secret' => TRUE));
    }
    return $result[$date];
  }

}
