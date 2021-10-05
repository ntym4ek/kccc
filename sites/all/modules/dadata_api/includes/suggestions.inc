<?php

/**
 * @file
 * Contains suggestions API.
 */

/**
 * Provides suggestions API.
 */
class DaDataApiSuggestions extends DaDataApiBase {

  /**
   * {@inheritdoc}
   */
  protected function getBaseUrl() {
    return 'https://suggestions.dadata.ru/suggestions/api/4_1/rs';
  }

  /**
   * Finds the nearest addresses by geographic coords.
   *
   * @param array $data
   *   The request parameters.
   * @param string $type
   *   Type of address to search ('address' or 'postal_unit').
   * @param string $method
   *   The request method ('GET' or 'POST'). Defaults to 'GET'.
   *
   * @return array|null
   *   Found addresses or NULL on failure.
   */
  public function geoLocate(array $data, $type, $method = 'GET') {
    $query = $options = array();

    if ($method === 'POST') {
      $options = array(
        'method' => $method,
        'data' => drupal_json_encode($data),
      );
    }
    else {
      $query = $data;
    }

    $url = $this->getRequestUrl("geolocate/$type", $query);
    return $this->sendRequest($url, $options);
  }

  /**
   * Finds the location by specified IP address.
   *
   * @param string|null $ip
   *   IP address to detect location.
   *
   * @return array|null
   *   Detected location or NULL on failure.
   */
  public function ipLocate($ip = NULL) {
    static $result = array();

    if ($ip === NULL) {
      $ip = ip_address();
    }

    if (!isset($result[$ip])) {
      $url = $this->getRequestUrl('iplocate/address', array('ip' => $ip));
      $result[$ip] = $this->sendRequest($url);
    }
    return $result[$ip];
  }

  /**
   * Finds various information by specified parameters.
   *
   * @param array $data
   *   The request parameters.
   * @param string $type
   *   Type of information to search.
   *
   * @return array|null
   *   Found information or NULL on failure.
   */
  public function findById(array $data, $type) {
    $options = array(
      'method' => 'POST',
      'data' => drupal_json_encode($data),
    );

    $url = $this->getRequestUrl("findById/$type");
    return $this->sendRequest($url, $options);
  }

  /**
   * Suggests various information by specified parameters.
   *
   * @param array $data
   *   The request parameters.
   * @param string $type
   *   Type of information to suggest.
   *
   * @return array|null
   *   Suggested information or NULL on failure.
   */
  public function suggest(array $data, $type) {
    $options = array(
      'method' => 'POST',
      'data' => drupal_json_encode($data),
    );

    $url = $this->getRequestUrl("suggest/$type");
    return $this->sendRequest($url, $options);
  }

}
