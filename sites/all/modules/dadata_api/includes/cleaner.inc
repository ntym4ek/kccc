<?php

/**
 * @file
 * Contains cleaner API.
 */

/**
 * Provides cleaner API.
 */
class DaDataApiCleaner extends DaDataApiBase {

  /**
   * {@inheritdoc}
   */
  protected function getBaseUrl() {
    return 'https://cleaner.dadata.ru/api/v1/clean';
  }

  /**
   * Cleans various information by specified parameters.
   *
   * @param array $data
   *   The request parameters.
   * @param string|null $type
   *   Type of information to clean.
   *
   * @return array|null
   *   Cleaned information or NULL on failure.
   */
  public function clean(array $data, $type = NULL) {
    $options = array(
      'method' => 'POST',
      'data' => drupal_json_encode($data),
      'secret' => TRUE,
    );

    $url = $this->getRequestUrl($type);
    return $this->sendRequest($url, $options);
  }

}
