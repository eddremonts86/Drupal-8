<?php

namespace Drupal\rp_stream_provider_info;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Stream Provider Info entities.
 *
 * @ingroup rp_stream_provider_info
 */
class StreamProviderInfoListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Stream Provider Info ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\rp_stream_provider_info\Entity\StreamProviderInfo */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.stream_provider_info.edit_form', [
          'stream_provider_info' => $entity->id(),
        ]
      )
    );
    return $row + parent::buildRow($entity);
  }

}
