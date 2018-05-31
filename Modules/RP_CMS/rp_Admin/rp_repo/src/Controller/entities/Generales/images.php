<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 3:23 PM
 */

namespace Drupal\rp_repo\Controller\entities\Generales;
use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_repo\Controller\entities\Generales\support;
class images extends ControllerBase
{
  public function getImg($url, $alias, $type = 'league')
  {
      $supportObj = new support();
    if (isset($url)) {
      $data = file_get_contents($url);

      if (isset($data)) {
        $file = file_save_data($data, "public://" . $alias . ".png", FILE_EXISTS_REPLACE);
      }
    } else {
      if ($type == "team") {
        $url = "http://eofcommunity.com/assets/img/default-team-logo.png";

      } else {
        $url = "http://www.brandemia.org/sites/default/files/sites/default/files/coib_logo_despues.jpg";
      }
      $data = file_get_contents($url);
      $file = file_save_data($data, "public://" . $alias . ".png", FILE_EXISTS_REPLACE);
    }

    $data_img = [
      'target_id' => $file->id(),
      'alt' => $supportObj->getClearUrl($alias),
      'title' => $supportObj->getClearUrl($alias),
    ];
    return $data_img;
  }
}
