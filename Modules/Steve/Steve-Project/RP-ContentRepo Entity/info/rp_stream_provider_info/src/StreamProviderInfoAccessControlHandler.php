<?php

namespace Drupal\rp_stream_provider_info;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Stream Provider Info entity.
 *
 * @see \Drupal\rp_stream_provider_info\Entity\StreamProviderInfo.
 */
class StreamProviderInfoAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished stream provider info entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published stream provider info entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit stream provider info entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete stream provider info entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add stream provider info entities');
  }

}
