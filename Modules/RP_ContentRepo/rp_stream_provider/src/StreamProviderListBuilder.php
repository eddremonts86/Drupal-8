<?php

namespace Drupal\rp_stream_provider;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Stream Provider entities.
 *
 * @ingroup rp_stream_provider
 */
class StreamProviderListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Stream Provider ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\rp_stream_provider\Entity\StreamProvider */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.stream_provider.edit_form', [
          'stream_provider' => $entity->id(),
        ]
      )
    );
    return $row + parent::buildRow($entity);
  }

}
