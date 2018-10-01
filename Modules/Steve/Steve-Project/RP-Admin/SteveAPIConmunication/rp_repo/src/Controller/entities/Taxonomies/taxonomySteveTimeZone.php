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
    $allTZones = $rpClient->getTimezones(); //get Schedule from API(JSON)
    foreach ($allTZones as $tZone) {
      $obj = [
        'field_api_id' => $tZone['id'],
        'name' => $tZone['name'],
        'vid' => 'steve_timezones',
      ];
      if (!($this->getTaxonomyByOBj($obj))) {
        print "New Time Zone - ".$tZone['name']."\n";
        $this->createGenericTaxonomy($obj);
      }
    }
  }

  public function importTimeZone($api_id) {
    $obj=['id' => $api_id];
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $tZone = $rpClient->getTimezone($obj); //get Schedule from API(JSON)
      $obj = [
        'field_api_id' => $tZone['id'],
        'name' => $tZone['name'],
        'vid' => 'steve_timezones',
      ];
      if (!($this->getTaxonomyByOBj($obj))) {
        print "New Time Zone - ".$tZone['name']."\n";
        $this->createGenericTaxonomy($obj);

    }
  }


  public function importTimeZoneByOBJ($tZone) {
    $obj = [
      'field_api_id' => $tZone['id'],
      'name' => $tZone['name'],
      'vid' => 'steve_timezones',
    ];
    if (!($this->getTaxonomyByOBj($obj))) {
      print "New Time Zone - ".$tZone['name']."\n";
      $this->createGenericTaxonomy($obj);

    }
  }


}
