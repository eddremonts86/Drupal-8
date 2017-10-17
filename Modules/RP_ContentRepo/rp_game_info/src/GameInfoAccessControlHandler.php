<?php

namespace Drupal\rp_game_info;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Game info entity.
 *
 * @see \Drupal\rp_game_info\Entity\GameInfo.
 */
class GameInfoAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_game_info\Entity\GameInfoInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished game info entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published game info entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit game info entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete game info entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add game info entities');
  }

}
