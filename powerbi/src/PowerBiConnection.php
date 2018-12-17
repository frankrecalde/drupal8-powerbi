<?php

namespace Drupal\powerbi;

/**
 * Class PowerBiConnection
 *
 * @package Drupal\powerbi
 */
class PowerBiConnection
{
  private $resource;
  /**
   * @var \Drupal\Core\Config\Config Power Bi settings
   */
  protected $config = null;

  /**
   * @var array Store sensitive API info such as the password
   */
  protected $sensitiveConfig = [];

  /**
   * PowerBiConnection constructor.
   */
  public function __construct()
  {
    $this->config = \Drupal::config('powerbi.settings');
    $this->resource = 'https://analysis.windows.net/powerbi/api';
  }

  /**
   * Get configuration or state setting for this Power BI integration module.
   *
   * @param string $name this module's config or state.
   *
   * @return mixed
   */
  protected function getConfig($name)
  {
    $sensitive = [
      'password',
    ];
    if (in_array($name, $sensitive)) {
      if (isset($this->sensitiveConfig[$name])) {
        return $this->sensitiveConfig[$name];
      }
      $this->sensitiveConfig[$name] = \Drupal::state()
        ->get('powerbi.' . $name);
      return $this->sensitiveConfig[$name];
    }
    return $this->config->get('powerbi.' . $name);
  }

  /**
   * Get access token
   *
   * @return mixed
   */
  public function getAccessToken()
  {
    $request_method = 'POST';
    $endpoint = "https://login.microsoftonline.com/common/oauth2/token";

    $client = \Drupal::httpClient();
    $options = [
      'form_params' => [
        'grant_type' => 'password',
        'scope' => 'openid',
        'resource' => $this->resource,
        'client_id' => $this->getConfig('client_id'),
        'client_secret' => $this->getConfig('client_secret'),
        'username' => $this->getConfig('username'),
        'password' => $this->getConfig('password')
      ]
    ];

    try {
      $response = $client->request($request_method, $endpoint, $options);
      $tokenResult = json_decode($response->getBody(), true);
      $token = $tokenResult['access_token'];
      $embeddedToken = 'Bearer ' . ' ' . $token;
      setcookie('access_token', $token, $tokenResult['expires_on']);
      return $token;
    } catch (\Exception $e) {
      watchdog_exception('powerbi', $e);
    }
  }

  /**
   * Use the access token to get an embedded URL using a GET request.
   *
   * @param [type] $url
   * @param [type] $method
   * @param [type] $displayType
   * @return mixed
   */
  public function getEmbeddedUrl($url, $request_method, $displayType)
  {
    // Get token from cookie instead of making request each time until it expired.
    $access_token = (isset($_COOKIE["access_token"])) ? $_COOKIE["access_token"] : self::getAccessToken();
    $options = [
      'headers' => [
        'Authorization' => 'Bearer ' . $access_token,
        'Cache-Control' => 'no-cache',
      ]
    ];

    $endpoint = $url . $this->getConfig('group_id') . '/' . $displayType;
    $client = \Drupal::httpClient();

    try {
      $response = $client->request($request_method, $endpoint, $options);
      $embedResponse = json_decode($response->getBody(), true);
      return $embedResponse;
    } catch (\Exception $e) {
      watchdog_exception('powerbi', $e);
    }
  }

}
