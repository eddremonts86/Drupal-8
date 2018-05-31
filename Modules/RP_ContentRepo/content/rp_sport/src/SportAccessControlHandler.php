<?php

namespace Drupal\rp_sport;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Sport entity.
 *
 * @see \Drupal\rp_sport\Entity\Sport.
 */
class SportAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_sport\Entity\SportInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished sport entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published sport entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit sport entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete sport entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add sport entities');
  }

}
