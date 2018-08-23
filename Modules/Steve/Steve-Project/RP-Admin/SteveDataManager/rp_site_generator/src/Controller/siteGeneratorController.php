<?php

namespace Drupal\rp_site_generator\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveSite;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class siteGeneratorController.
 */
class siteGeneratorController extends ControllerBase {

  /**
   * Sitelist.
   *
   * @return string
   *   Return Hello string.
   */
  public function __construct() {
    define('ROOTPATH', __DIR__);
  }

  public function siteList() {
    $taxonomy =  new taxonomySteveSite();
    $sites =  $taxonomy->getAllSite();
    return $sites;
  }

  public function renderTable( )
  {
    $results=  $this->siteList();
    $data = array();
    if (is_array($results)) {
      foreach ($results as $result) {

        if(count($result['fileConfig']) >= 1)
          $OPERATIONS = ['#markup' => $this->t('<a href="/admin/siteGenerator/' . $result['apiID'] . '" class="button js-form-submit form-submit">Create a new version</a>')];
        else
          $OPERATIONS = ['#markup' => $this->t('')];

        $data[] = array(
          'sites' => ['#markup' => $result['name']],
          'type' => $result['site_group'],
          'url' => $result['siteURL'] ? $result['siteURL'] : 'Please add a Site URL ',
          'configNumber' => count($result['fileConfig']),
          'OPERATIONS' => $OPERATIONS
        );
      }
    }
    return $data;
  }

  public function siteGenerator(Request $request) {
    $SiteId = $request->attributes->get('siteID');
    return $this->Generator($SiteId);

  }


  public function generateAllSites(){
    $taxonomy =  new taxonomySteveSite();
    $obj = [
      'vid' => 'steve_site'
    ];
    $sites =  $taxonomy->getTaxonomyByOBj($obj);
    $sites = $taxonomy->sitesFormat($sites);
    foreach ($sites as $site){
      $conf = @file_get_contents(end($site[0]["fileConfig"]));
      if($conf){
        $this->Generator($site);
        }
      }
      return true;
    }



  public function Generator($SiteId) {
    $taxonomy =  new taxonomySteveSite();
    $obj = [
            'field_api_id' => $SiteId,
            'vid' => 'steve_site'
    ];
    $sites =  $taxonomy->getTaxonomyByOBj($obj);
    $site = $taxonomy->sitesFormat($sites);
    $data = json_decode(file_get_contents(end($site[0]["fileConfig"])));
     if(!isset($data)){
        $data = json_decode(file_get_contents(end($site[0]["siteConfigurations"])["value"]));
     }
    $multiSite = $data->multisite;
    $shArray = array();
    foreach ($data as $key => $d ){$shArray[] =$d;}
    $shArray = str_replace(' ',"_",$shArray);
    $shArray = implode(' ', $shArray);
    $logfile = ROOTPATH.'/extFiles/logs/log-'.$multiSite.'.txt';
    if ($shArray){
        if($data->base != null ){
          $takeoldconfg  = ROOTPATH.'/extFiles/shScript/takeoldconfg.sh';
          shell_exec("sh $takeoldconfg $shArray  >$logfile 2>&1");
          }
        else{
          $newSite  = ROOTPATH.'/extFiles/shScript/siteGenerator.sh';
          shell_exec("sh $newSite $shArray >$logfile 2>&1");
        }
        $goTo = "<a class='button button--primary js-form-submit form-submit' target='_blank' href='http://$multiSite'>Go to  $multiSite now !!!</a>";
        return ['#type' => 'markup','#markup' => $goTo];
    }
    else{
      return ['#type' => 'markup','#markup' => 'Please check your site configuration.'];
    }
  }
}


