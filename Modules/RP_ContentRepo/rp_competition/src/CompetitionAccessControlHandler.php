<?php

namespace Drupal\rp_competition;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Competition entity.
 *
 * @see \Drupal\rp_competition\Entity\Competition.
 */
class CompetitionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_competition\Entity\CompetitionInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished competition entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published competition entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit competition entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete competition entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add competition entities');
  }

}
