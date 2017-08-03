<?php

namespace Drupal\rp_site_info\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Site info config entity.
 *
 * @ConfigEntityType(
 *   id = "site_info_config",
 *   label = @Translation("Site info config"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\rp_site_info\SiteInfoConfigListBuilder",
 *     "form" = {
 *       "add" = "Drupal\rp_site_info\Form\SiteInfoConfigForm",
 *       "edit" = "Drupal\rp_site_info\Form\SiteInfoConfigForm",
 *       "delete" = "Drupal\rp_site_info\Form\SiteInfoConfigDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\rp_site_info\SiteInfoConfigHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "site_info_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/rp/entity-content/site_info_config/{site_info_config}",
 *     "add-form" = "/admin/rp/entity-content/site_info_config/add",
 *     "edit-form" = "/admin/rp/entity-content/site_info_config/{site_info_config}/edit",
 *     "delete-form" = "/admin/rp/entity-content/site_info_config/{site_info_config}/delete",
 *     "collection" = "/admin/rp/entity-content/site_info_config"
 *   }
 * )
 */
class SiteInfoConfig extends ConfigEntityBase implements SiteInfoConfigInterface {

  /**
   * The Site info config ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Site info config label.
   *
   * @var string
   */
  protected $label;

}
