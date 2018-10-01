<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 4:48 PM
 */

namespace Drupal\rp_repo\Controller\entities\Import;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\entities\Generales\apiConfig;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyJsonLD;
use Drupal\rp_repo\Controller\entities\Taxonomies\TaxonomyChannel;
use Drupal\rp_repo\Controller\entities\Pages\Events;

class fromApi extends ControllerBase {


  public function importApiData($date = '', $days = 0) {
    $getInfoObj = new apiConfig();
    if ($date != '' and $days == 0) {
      $parametersList = $getInfoObj->getConfig($date, 1);
    }
    elseif ($days != 0) {
      $parametersList = $getInfoObj->getConfig($date = 'Y-m-d', $days);
    }
    else {
      $parametersList = $getInfoObj->getConfig();
    }

    /*Creating Channels on Content Type  "Cannels"*/
    foreach ($parametersList as $parameters) {
      $startday = $parameters['start'];
      for ($i = 0; $i < $parameters['days']; $i++) {
        $newDate = strtotime('+' . $i . ' day', strtotime($startday));
        $date = date('Y-m-d', $newDate);
        $parameters['start'] = $date;
        echo 'Import data from ' . $date . "\n";
        $this->importSchedule($parameters);
        echo 'Import all data from ' . $date . "\n";
      }
    }
    return TRUE;
  }

  public function importSchedule($parameters) {}

  public function importChannels() {
    $tax = new TaxonomyChannel();
    $tax->getAllchannels();
    return TRUE;
  }

  public function importJsonLd() {
    $tax = new taxonomyJsonLD();
    $tax->createJsonLdTaxonomy();
    return TRUE;
  }

}
