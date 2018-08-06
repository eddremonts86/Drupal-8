<?php

namespace Drupal\rp_competition_info;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Competition Info entity.
 *
 * @see \Drupal\rp_competition_info\Entity\CompetitionInfo.
 */
class CompetitionInfoAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_competition_info\Entity\CompetitionInfoInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished competition info entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published competition info entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit competition info entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete competition info entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add competition info entities');
  }

}
