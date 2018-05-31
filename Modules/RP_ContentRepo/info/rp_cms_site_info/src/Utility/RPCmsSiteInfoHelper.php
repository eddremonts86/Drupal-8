<?php


namespace Drupal\rp_cms_site_info\Utility;


/**
 * Class RPCmsSiteInfoHelper
 * @package Drupal\rp_cms_site_info\Utility
 */
class RPCmsSiteInfoHelper
{

  /**
   * Get first one site info config
   *
   * @param string $name
   *
   * @return mixed|string
   */
  public static function getSiteInfoConfig($name){
    $value = '';

    /** @var $entity */
    $entity = \Drupal::entityManager()->getStorage('cms_site_info');

    $infos = $entity->loadMultiple();


    if(count($infos)) {
      $info = current($infos);
      $value = self::getSiteInfoValue($info, $name);
    }


    return is_null($value) ? '' : $value;

  }

  /**
   * Get specific stite info config
   *
   * @param string $name
   * @param string $region
   * @param string $language_content
   *
   * @return mixed|string
   */
  public static function getSiteInfoConfigSpec($name, $region, $language_content){
    $value = '';

    /** @var $entity */
    $entity = \Drupal::entityManager()->getStorage('cms_site_info');

    $infos = $entity->loadByProperties(['region' => $region, 'language_content' => $language_content]);

    if(count($infos)) {
      $info = current($infos);
      $value = self::getSiteInfoValue($info, $name);
    }

    return is_null($value) ? '' : $value;

  }

  /**
   * @param $info
   * @param $name
   * @return mixed
   */
  public static function getSiteInfoValue($info, $name) {

    $value = $info->get($name);
    if(is_null($value)) {
      $resource_fields = $info->get('resourceFields');
      if(isset($resource_fields[$name]['fieldValue'])) {
        $value = $resource_fields[$name]['fieldValue'];
      }
      elseif(isset($resource_fields['field_site_info_'.$name]['fieldValue'])){
        $value = $resource_fields['field_site_info_'.$name]['fieldValue'];
      }
    }

    return $value;

  }

  /**
   *
   */
  public static function getSiteInfoCombos() {

    //$entity = \Drupal::entityManager()->getStorage('cms_site_info')->create();
    $entities = \Drupal::entityManager()->getStorage('cms_site_info')->loadMultiple();

    if(empty($entities))
      return false;

    /* var $config \Drupal\Core\Config\ConfigFactoryInterface */
    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api_id = $config->get('rp_base_site_api_id');

    $combos = [];
    foreach($entities as $site_info) {
      if($site_info->get('site') == $site_api_id) {
        $region_id = $site_info->get('region');
        $language_id = $site_info->get('language_content');
        $combos[] = [$region_id, $language_id];
      }
    }

    return $combos;

  }

}