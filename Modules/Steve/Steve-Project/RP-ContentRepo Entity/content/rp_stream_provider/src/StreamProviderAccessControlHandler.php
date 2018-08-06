<?php

namespace Drupal\rp_stream_provider;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Stream Provider entity.
 *
 * @see \Drupal\rp_stream_provider\Entity\StreamProvider.
 */
class StreamProviderAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_stream_provider\Entity\StreamProviderInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished stream provider entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published stream provider entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit stream provider entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete stream provider entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add stream provider entities');
  }

}
