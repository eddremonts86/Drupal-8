<?php

namespace Drupal\rp_cms_site_info\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the CMS Site Info entity.
 *
 * @ConfigEntityType(
 *   id = "cms_site_info",
 *   label = @Translation("CMS Site Info"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\rp_cms_site_info\CmsSiteInfoListBuilder",
 *     "form" = {
 *       "add" = "Drupal\rp_cms_site_info\Form\CmsSiteInfoForm",
 *       "edit" = "Drupal\rp_cms_site_info\Form\CmsSiteInfoForm",
 *       "delete" = "Drupal\rp_cms_site_info\Form\CmsSiteInfoDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\rp_cms_site_info\CmsSiteInfoHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "cms_site_info",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/rp/entity-content/cms_site_info/{cms_site_info}",
 *     "add-form" = "/admin/rp/entity-content/cms_site_info/add",
 *     "edit-form" = "/admin/rp/entity-content/cms_site_info/{cms_site_info}/edit",
 *     "delete-form" = "/admin/rp/entity-content/cms_site_info/{cms_site_info}/delete",
 *     "collection" = "/admin/rp/entity-content/cms_site_info"
 *   }
 * )
 */
class CmsSiteInfo extends ConfigEntityBase implements CmsSiteInfoInterface {

  /**
   * The CMS Site Info ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The CMS Site Info label.
   *
   * @var string
   */
  protected $label;

  public function getFieldApiId(){
    return $this->get('id')->value;
  }

}
