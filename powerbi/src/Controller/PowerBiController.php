<?php

namespace Drupal\powerbi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\powerbi\PowerBiConnection;

/**
 * Provides controller methods for the Power BI API integration.
 */
class PowerBiController extends ControllerBase
{
  const _REQUEST_METHOD = 'GET';

  public function showDashboard($display)
  {
    $url = "https://api.powerbi.com/v1.0/myorg/groups/";
    $powerbi = new PowerBiConnection();
    $method = self::_REQUEST_METHOD;
    $embedData = $powerbi->getEmbeddedUrl($url, $method, $display);

    $config = \Drupal::config('powerbi.settings');
    $state = \Drupal::state();
    $debuMode = $config->get('powerbi.debug');
    $content = [
      '#theme' => 'powerbi_dashboard',
      '#debug' => $debuMode,
      '#response' => $embedData,
    ];

    // Get access token from cookie instead of making call to the server.
    $access_token = (isset($_COOKIE["access_token"])) ? $_COOKIE["access_token"] : $powerbi->getAccessToken();
    $data = array(
      'powerBI' => $embedData,
      'accessToken' => $access_token,
      'debug' => $debuMode,
      'display' => 'dashboard'
    );

    //Add a JS library
    $content['#attached']['library'][] = 'powerbi/powerbi-render';
    $content['#attached']['drupalSettings']['powerbi'] = $data;

    return $content;
  }

  public function showReports($display)
  {
    $url = "https://api.powerbi.com/v1.0/myorg/groups/";
    $powerbi = new PowerBiConnection();
    $method = self::_REQUEST_METHOD;
    $embedData = $powerbi->getEmbeddedUrl($url, $method, $display);

    $config = \Drupal::config('powerbi.settings');
    $state = \Drupal::state();
    $debuMode = $config->get('powerbi.debug');
    $content = [
      '#theme' => 'powerbi_report',
      '#debug' => $debuMode,
      '#response' => $embedData,
    ];

    // Get access token from cookie instead of making call to the server.
    $access_token = (isset($_COOKIE["access_token"])) ? $_COOKIE["access_token"] : $powerbi->getAccessToken();
    $data = array(
      'powerBI' => $embedData,
      'accessToken' => $access_token,
      'debug' => $debuMode,
      'display' => 'report'
    );

    //Add a JS library
    $content['#attached']['library'][] = 'powerbi/powerbi-render';
    $content['#attached']['drupalSettings']['powerbi'] = $data;

    return $content;
  }

}
