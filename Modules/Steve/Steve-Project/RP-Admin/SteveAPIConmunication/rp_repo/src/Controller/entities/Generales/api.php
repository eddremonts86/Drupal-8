<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 9/7/18
 * Time: 12:53 PM
 */

namespace Drupal\rp_repo\Controller\entities\Generales;
use Drupal\rp_rollbar\rollbarReport;


abstract class api {

  public function __construct() {
    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api = $config->get('rp_base_site_server');
    if($site_api == 0){
      $i = new rollbarReport();
      $i->alert('Trying to get access to a site with out any Contents API');
      exit();
    }
  }


}
