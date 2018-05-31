<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 23-02-18
 * Time: 12:06
 */

namespace Drupal\rp_repo\Controller\entities\Generales;
use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_cms_site_info\Utility\RPCmsSiteInfoHelper;

class apiConfig extends ControllerBase
{
  public function getConfig($date = 'Y-m-d', $days = 15)
  {
    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api_id = $config->get('rp_base_site_api_id');
    $data = RPCmsSiteInfoHelper::getSiteInfoCombos();
    $date = date($date);
    $site = $site_api_id;


    if ($site_api_id or $date) {
      foreach ($data as $siteInfo) {
        $paramList [] = [
          'site' => $site,
          'region' => $siteInfo[0],
          'lang' => $siteInfo[1],
          'start' => $date,
          'days' => $days,
          'tz' => date_default_timezone_get(),
        ];
      }
      return $paramList;
    } else {
      echo "Please make a basic configuration of the site \n";;
      return FALSE;
    }

  }

}
