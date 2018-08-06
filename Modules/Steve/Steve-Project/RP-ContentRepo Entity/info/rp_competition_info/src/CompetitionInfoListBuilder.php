<?php

namespace Drupal\rp_competition_info;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Competition Info entities.
 *
 * @ingroup rp_competition_info
 */
class CompetitionInfoListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Competition Info ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\rp_competition_info\Entity\CompetitionInfo */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.competition_info.edit_form', [
          'competition_info' => $entity->id(),
        ]
      )
    );
    return $row + parent::buildRow($entity);
  }

}
