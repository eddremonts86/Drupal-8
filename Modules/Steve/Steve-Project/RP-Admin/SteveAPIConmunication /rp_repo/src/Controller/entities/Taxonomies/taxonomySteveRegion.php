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
    $allsites = $rpClient->getRegions(); //get Schedule from API(JSON)
    foreach ($allsites as $site) {
      $obj = [
        'field_api_id' => $site['id'],
        'name' => $site['name'],
        'field_code' => $site['code'],
        'field_parent' => $site['parent']? $site['parent']: " ",
        'vid' => 'steve_regions',
      ];
      if (!($this->getTaxonomyByOBj($obj))) {
        $this->createGenericTaxonomy($obj);
      }
    }
  }

  public function importRegion($api_id) {
    $obj=['id'=>$api_id];
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $site = $rpClient->getRegion($obj); //get Schedule from API(JSON)
      $obj = [
        'field_api_id' => $site['id'],
        'name' => $site['name'],
        'field_code' => $site['code'],
        'field_parent' => $site['parent']? $site['parent']: " ",
        'vid' => 'steve_regions',
      ];
      if (!($this->getTaxonomyByOBj($obj))) {
        $this->createGenericTaxonomy($obj);

    }
  }

}
