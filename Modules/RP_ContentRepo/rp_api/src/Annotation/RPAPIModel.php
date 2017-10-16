<?php

namespace Drupal\rp_api\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a RPAPIModel annotation object.
 *
 * @see \Drupal\rp_api\RPAPIModelManager
 * @see plugin_api
 *
 * @Annotation
 */
class RPAPIModel extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * A brief, human readable, description of the RPAPIModel type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * The entity name.
   *
   * @var string
   */
  public $entity;

  /**
   * The api method.
   *
   * @var string
   */
  public $api_method;

  /**
   * The global wrapper results.
   *
   * @var string
   */
  public $global_wrapper = '';

  /**
   * The item wrapper results.
   *
   * @var string
   */
  public $item_wrapper = '';

  /**
   * The result is single.
   *
   * @var string
   */
  public $single = FALSE;


}
