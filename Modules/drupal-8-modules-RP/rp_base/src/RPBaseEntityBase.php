<?php

namespace Drupal\rp_base;

use Drupal\Core\Entity\RevisionableContentEntityBase;

/**
 * Class RPBaseEntityBase
 * @package Drupal\rp_base
 */
abstract class RPBaseEntityBase extends RevisionableContentEntityBase  {

  /**
   * @return mixed|null|string
   */
  public function label() {
    if(!is_null($this->getApiCode()))
      return $this->getApiCode();
    else
      return parent::label();
  }

  /**
   * Get API Code
   */
  public function getApiCode() {
    if($this->hasField($this->getFieldApiCode()))
      return $this->get($this->getFieldApiCode())->value;
    else
      return null;
  }

  /**
   * Get API ID value
   */
  public function getApiId() {
    if($this->hasField($this->getFieldApiId()))
      return $this->get($this->getFieldApiId())->value;
    else
      return null;
  }

  /**
   * Get API Code Field
   */
  public function getFieldApiCode() {
    $name = $this->getEntityTypeId();
    return 'field_'.$name.'_code';
  }

  /**
   * Get API ID Field
   */
  public function getFieldApiId() {
    $name = $this->getEntityTypeId();
    return 'field_'.$name.'_api_id';
  }

}