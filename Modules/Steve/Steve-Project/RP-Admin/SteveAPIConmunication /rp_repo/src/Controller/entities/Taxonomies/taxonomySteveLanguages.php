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


class taxonomySteveLanguages extends taxonomy
{
  public function importLanguage($api_id)
  {
    $obj = ['id' =>$api_id];
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $language = $rpClient->getLanguage($obj); //get Schedule from API(JSON)
    $obj = [
        'field_api_id' => $language['id'],
        'name' => $language['name'],
        'field_code' => $language['code'],
        'field_locale' => $language['locale']? $language['locale']: " ",
        'vid' => 'steve_languages',
      ];
      if (!($this->getTaxonomyByOBj($obj))) {
        print "New Lang - ". $language['name']."\n";
        $this->createGenericTaxonomy($obj);
      }
    return TRUE;
  }

  public function importLanguages()
  {
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $alllanguages = $rpClient->getLanguages(); //get Schedule from API(JSON)
    foreach ($alllanguages as $language) {
      $obj = [
        'field_api_id' => $language['id'],
        'name' => $language['name'],
        'field_code' => $language['code'],
        'field_locale' => $language['locale']? $language['locale']: " ",
        'vid' => 'steve_languages',
      ];
      if (!($this->getTaxonomyByOBj($obj))) {
        print "New Lang - ". $language['name']."\n";
        $this->createGenericTaxonomy($obj);
      }
    }
    return true;
  }
}
