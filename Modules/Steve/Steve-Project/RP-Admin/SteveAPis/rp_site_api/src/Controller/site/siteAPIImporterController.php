<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 7/30/18
 * Time: 1:26 PM
 */

namespace Drupal\rp_site_api\Controller\site;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveSite;
use  Drupal\rp_site_api\Controller\api\siteRepoService;
use Drupal\taxonomy\Entity\Term;

class siteAPIImporterController  extends ControllerBase {

  public function importAllSites(){
    $site = siteRepoService::getClient();
    $obj = $site->getAllSite(false);
  foreach ($obj['data']['sites'] as $ob){
      $this->updateSite($ob);
    }
    return TRUE;
  }
  public function importSiteByID($id){
    $site = siteRepoService::getClient();
    $obj=array('site' => $id);
    $obj = $site->getSitebyID($obj);
    $this->updateSite($obj['data']['site'][0]);
    return TRUE;
  }

  public function  updateSite($siteNEW){
      $site = new taxonomySteveSite();
      $site->importSite($siteNEW['apiID']);
      $obj = ['vid' => 'steve_site', 'field_api_id' => $siteNEW['apiID']];
      $id = $site->getTaxonomyByOBj($obj, 1);
      $TaxonomySite = Term::load($id);
      $TaxonomySite->field_site_configuration = $siteNEW['fileConfig'];
      $TaxonomySite->field_site_url = $siteNEW['siteURL'];
      $TaxonomySite->field_site_token = $siteNEW['token'];
      $TaxonomySite->description = array(
                            'format'=> 'basic_html',
                            'value'=> $siteNEW['bodyDesc']
                          ) ;
      $TaxonomySite->save();
      return TRUE;
  }
}
