<?php

namespace Drupal\rp_cms_channels_by_contenttype;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Channels_by_contenttype entity.
 *
 * @see \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttype.
 */
class channels_by_contenttypeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished channels_by_contenttype entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published channels_by_contenttype entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit channels_by_contenttype entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete channels_by_contenttype entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add channels_by_contenttype entities');
  }

}
