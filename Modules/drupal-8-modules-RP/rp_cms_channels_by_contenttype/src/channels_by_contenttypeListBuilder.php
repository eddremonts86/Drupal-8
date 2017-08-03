<?php

namespace Drupal\rp_cms_channels_by_contenttype;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Channels_by_contenttype entities.
 *
 * @ingroup rp_cms_channels_by_contenttype
 */
class channels_by_contenttypeListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Channels_by_contenttype ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttype */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.channels_by_contenttype.edit_form',
      ['channels_by_contenttype' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
