<?php

namespace Drupal\rp_cms_site_info\TwigExtension;

use Drupal\rp_cms_site_info\Utility\RPCmsSiteInfoHelper;

/**
 * Class RpSiteInfoTwigExtension.
 *
 * @package Drupal\rp_cms_site_info
 */
class RPSiteInfoTwigExtension extends \Twig_Extension {

  /**
  * {@inheritdoc}
  */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('rp_site_info', [$this, 'getRPSiteInfoConfig']),
    ];
  }

  /**
   * @param $name
   * @param null $region
   * @param null $language_content
   *
   * @return mixed|string
   */
  public function getRPSiteInfoConfig($name, $region = NULL, $language_content = NULL){

    if(is_null($region) && is_null($language_content))
      $value = RPCmsSiteInfoHelper::getSiteInfoConfig($name);
    else
      $value = RPCmsSiteInfoHelper::getSiteInfoConfigSpec($name, $region, $language_content);

    return is_null($value) ? '' : $value;
  }

 /**
  * {@inheritdoc}
  */
  public function getName() {
    return 'rp_site_info.twig.extension';
  }

}
