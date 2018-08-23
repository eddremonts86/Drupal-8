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
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveRegion;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveLanguages;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveTimeZone;
use Drupal\rp_repo\Controller\entities\Taxonomies\TaxonomyChannel;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyStream;
use \Drupal\file\Entity\File;

class taxonomySteveSite extends taxonomy {

  public function getSitebyToken($token) {
    $obj = [
      'field_site_token' => $token,
      'vid' => 'steve_site',
    ];
    $site = $this->getTaxonomyByOBj($obj);
    if (isset($site) and !empty($site)) {
      return $site;
    }
    else {
      return FALSE;
    }
  }

  public function importSite($api_id = NULL, $allsites = NULL) {
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    if (!isset($allsites)) {
      $siteID = ['id' => $api_id];
      $allsites = $rpClient->getSite($siteID); //get Schedule from API(JSON)
    }
    else {
      $siteID = ['id' => $allsites['id']];
    }

    if (isset($siteID) and isset($allsites)) {

      $allsitesChannels = $rpClient->getSiteChannel($siteID);
      $channels = new TaxonomyChannel();
      $channels->getchannelsbysyte($allsitesChannels);

      $allsitesLanguges = $rpClient->getSiteLanguges($siteID);
      $langugue = new taxonomySteveLanguages();
      foreach ($allsitesLanguges as $sitesLanguges) {
        $langugue->importLanguage($sitesLanguges['id']);
      }

      $allsitesRegions = $rpClient->getSiteRegions($siteID);
      $Regions = new taxonomySteveRegion();
      foreach ($allsitesRegions as $sitesRegions) {
        $Regions->importRegion($sitesRegions['id']);
      }

      $allsitesStreams = $rpClient->getSiteStreams($siteID);
      $streams = new taxonomyStream();
      foreach ($allsitesStreams as $sitesStreams) {
        $streams->createStreamByID($sitesStreams['id']);
      }
      /*
       *  I need a endpoint to site tizone by id like
        http://steveapi.rebelpenguin.dk:8000/api/sites/15/timezone/
      */

      $t_zone = new taxonomySteveTimeZone ();
      $t_zone->importTimeZoneByOBJ($allsites["timezone"]);

      $this->createSite($allsites);
      return TRUE;
    }
    return FALSE;
  }

  public function importSites() {
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $allsites = $rpClient->getSites(); //get Schedule from API(JSON)
    foreach ($allsites as $site) {
      $this->importSite(NULL, $site);
    }
    return TRUE;
  }

  public function createSite($site) {
    /*Channeles*/
    $tags_channels = [];
    foreach ($site['channels'] as $channels) {
      $obj = ['vid' => 'channels', 'field_channel_api_id' => $channels];
      $id = $this->getTaxonomyByOBj($obj, 1);
      if ($id) {
        $tags_channels [] = ['target_id' => $id];
      }
    }
    /*languages*/
    $tags_languages = [];
    foreach ($site['languages'] as $languages) {
      $obj = ['vid' => 'steve_languages', 'field_locale' => $languages];
      $id = $this->getTaxonomyByOBj($obj, 1);
      if ($id) {
        $tags_languages [] = ['target_id' => $id];
      }
    }

    /*regions*/
    $tags_regions = [];
    foreach ($site['regions'] as $regions) {
      $obj = ['vid' => 'steve_regions', 'field_code' => $regions];
      $id = $this->getTaxonomyByOBj($obj, 1);
      if ($id) {
        $tags_regions [] = ['target_id' => $id];
      }
    }

    /**timezone**/
    $timezone = [];
    $obj = ['vid' => 'steve_timezones', 'name' => $site['timezone']];
    $id = $this->getTaxonomyByOBj($obj, 1);
    if ($id) {
      $timezone [] = ['target_id' => $id];
    }

    $uuid_service = \Drupal::service('uuid');
    $uuid = $uuid_service->generate();


    $obj = [
      'field_api_id' => $site['id'],
      'name' => $site['name'],
      'field_site_group' => $site['site_group'],
      'field_channels' => $tags_channels,
      'field_languages' => $tags_languages,
      'field_languages' => $tags_languages,
      'field_regions' => $tags_regions,
      'field_time_zone' => $timezone,
      'field_site_token' => $uuid,
      'vid' => 'steve_site',
    ];
    $objT = [
      'vid' => 'steve_site',
      'field_api_id' => $site['id'],
    ];
    if (!($this->getTaxonomyByOBj($objT))) {
      print "New Site - ".$site['name']."\n";;
      $this->createGenericTaxonomy($obj);
    }
    return TRUE;
  }

  public function getSites($apiId = FALSE){

    if($apiId)
    {$obj = ['field_api_id' => $apiId,'vid' => 'steve_site'];}
    else {$obj = ['vid' => 'steve_site'];}

    $sites = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties($obj);
    $all_site =  $this->sitesFormat($sites);
  return $all_site;
  }

  public function getAllSite(){
    $obj = [
      'vid' => 'steve_site',
    ];
    $site = $this->getTaxonomyByOBj($obj);
    $site = $this->sitesFormat($site);
    return $site;
  }

  public function sitesFormat($sites){
      $all_site =array();
         foreach ($sites as $site){
           $channels = array();
              foreach ($site->field_channels as $channel){
                $obj = ['tid' => $channel->target_id,'vid' => 'channels'];
                $sites = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties($obj);
                $sites = reset( $sites);
                $channels [] = [
                    'id'=> $sites->field_channel_api_id->value,
                    'name'=> $sites->name->value
                  ];
                }

          $languages= array();
          foreach ($site->field_languages as $language){
            $obj = ['tid' => $language->target_id,'vid' => 'steve_languages'];
            $language = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties($obj);
            $language = reset( $language);
            $languages [] = array(
              'id'=>$language->field_api_id->value  ,
              'name' => $language->name->value
            );
          }

          $regions= array();
          foreach ($site->field_regions as $region){
            $obj = ['tid' => $region->target_id,'vid' => 'steve_regions'];
            $region = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties($obj);
            $region = reset( $region) ;
            $regions [] =  array(
              'id'=>$region->field_api_id->value  ,
              'name' => $region->name->value

            );

          }

          $timeZones= array();
          foreach ($site->field_time_zone as $timeZone){
            $obj = ['tid' => $timeZone->target_id,'vid' => 'steve_timezones'];
            $timeZone = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties($obj);
            $timeZone=  reset( $timeZone);
            $timeZones = array(
              'id'=>$timeZone->field_api_id->value  ,
              'name' => $timeZone->name->value
            );
          }

          $urlFile = array();
          foreach (($site->toArray())["field_nodes_ct"] as $file){
            $urlFile[] = $this->getFileURL($file["target_id"]);
          }

          $all_site[] = [
            'name' => $site->name->value,
            'apiID' => $site->field_api_id ->value,
            'site_group' => $site->field_site_group->value,
            'token' => $site->field_site_token->value,
            'siteURL' => $site->field_site_url->value,
            'bodyDesc' => $site->description[0]->value,
            'channels' =>  $channels,
            'languages' =>  $languages ,
            'regions' =>   $regions,
            'timeZones' => $timeZones ,
            'siteConfigurations' =>  ($site->toArray())["field_site_configuration"] ,
            'fileConfig' => $urlFile
          ];

      }
      return $all_site;
  }

  public function getFileURL($id) {
    $imgUrl = '';
    if (isset($id) and $id != NULL and $id != '') {
      $img = File::load($id)->toArray();
      $imgUrl = $img["uri"][0]["value"];
      $imgUrl = file_create_url($imgUrl);
    }
    return $imgUrl;
  }

}
