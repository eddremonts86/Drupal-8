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


class taxonomySteveRegion extends taxonomy
{

  public function importRegions() {
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $Regions = $rpClient->getRegions(); //get Schedule from API(JSON)
    foreach ($Regions as $region) {
      $obj = [
        'field_api_id' => $region['id'],
        'name' => $region['name'],
        'field_code' => $region['code'],
        'field_parent' => $region['parent']? $region['parent']: " ",
        'vid' => 'steve_regions',
      ];
      if (!($this->getTaxonomyByOBj($obj))) {
        print "New Region - ". $region['name']."\n";
        $this->createGenericTaxonomy($obj);
      }
    }
  }

  public function importRegion($api_id) {
    $obj=['id'=>$api_id];
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $region = $rpClient->getRegion($obj); //get Schedule from API(JSON)
      $obj = [
        'field_api_id' => $region['id'],
        'name' => $region['name'],
        'field_code' => $region['code'],
        'field_parent' => $region['parent']? $region['parent']: " ",
        'vid' => 'steve_regions',
      ];
      if (!($this->getTaxonomyByOBj($obj))) {
        print "New Region - ". $region['name']."\n";
        $this->createGenericTaxonomy($obj);

    }
  }

}
