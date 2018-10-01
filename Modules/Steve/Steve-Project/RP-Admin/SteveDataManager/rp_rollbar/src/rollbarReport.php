<?php

namespace Drupal\rp_rollbar;

use Rollbar\Rollbar;
use Rollbar\Payload\Level;
/**
 * Class rollbarReport.
 */
 class  rollbarReport implements rollbarReportInterface {
  /**
   * Constructs a new rollbarReport object.
   */
  public function __construct() {

    $config_factory = \Drupal::configFactory();
    $config = $config_factory->getEditable('rp_base.settings');

    $env = $config->get('rp_base_rollbar_environment');
    $key = $config->get('rp_base_rollbar_key');
    $access_token = $config->get('rp_base_rollbar_access_token');

    if ($env and $access_token) {

      switch ($env){
        case 1:
            $env = 'production';
        case 2:
            $env = 'testing';
        case 3:
            $env = 'development';
        default:
          $env = 'development';
       }

      Rollbar::init(
        [
          'access_token' => $access_token,
          'environment' => $env,
        ]
      );
    }
  }
  public static function info($menssage){Rollbar::log(Level::info(), $menssage);}
  public static function error($menssage){Rollbar::log(Level::error(), $menssage);}
  public function emergency($menssage){Rollbar::log(Level::emergency(), $menssage);}
  public function alert($menssage){Rollbar::log(Level::alert(), $menssage);}
  public function critical($menssage){Rollbar::log(Level::critical(), $menssage);}
  public function warning($menssage){Rollbar::log(Level::warning(), $menssage);}
  public function notice($menssage){Rollbar::log(Level::notice(), $menssage);}
  public function debug($menssage){Rollbar::log(Level::debug(), $menssage);}

}
