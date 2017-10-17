<?php

namespace Drupal\rp_sport_info;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Sport info entity.
 *
 * @see \Drupal\rp_sport_info\Entity\SportInfo.
 */
class SportInfoAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_sport_info\Entity\SportInfoInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished sport info entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published sport info entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit sport info entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete sport info entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add sport info entities');
  }

}
