<?php

namespace Drupal\rp_channel;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Channel entity.
 *
 * @see \Drupal\rp_channel\Entity\Channel.
 */
class ChannelAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_channel\Entity\ChannelInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished channel entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published channel entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit channel entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete channel entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add channel entities');
  }

}
