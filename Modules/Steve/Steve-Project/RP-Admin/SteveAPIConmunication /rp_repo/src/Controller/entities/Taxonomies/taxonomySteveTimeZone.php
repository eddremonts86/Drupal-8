<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 3:24 PM
 */


namespace Drupal\rp_repo\Controller\entities\Taxonomies;

use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomy;


class taxonomySteveTimeZone extends taxonomy {

  public function importTimeZones() {
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $all_t_zones = $rpClient->getTimezones(); //get Schedule from API(JSON)
    foreach ($all_t_zones as $_t_zone) {
      $obj = [
        'field_api_id' => $_t_zone['id'],
        'name' => $_t_zone['name'],
        'vid' => 'steve_timezones',
      ];
      if (!($this->getTaxonomyByOBj($obj))) {
        $this->createGenericTaxonomy($obj);
      }
    }
  }

  public function importTimeZone($api_id) {
    $obj=['id' => $api_id];
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $_t_zone = $rpClient->getTimezone($obj); //get Schedule from API(JSON)
      $obj = [
        'field_api_id' => $_t_zone['id'],
        'name' => $_t_zone['name'],
        'vid' => 'steve_timezones',
      ];
      if (!($this->getTaxonomyByOBj($obj))) {
        $this->createGenericTaxonomy($obj);

    }
  }

}
