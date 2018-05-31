<?php

namespace Drupal\rp_region;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Region entity.
 *
 * @see \Drupal\rp_region\Entity\Region.
 */
class RegionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_region\Entity\RegionInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished region entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published region entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit region entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete region entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add region entities');
  }

}
