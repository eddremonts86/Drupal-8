<?php

namespace Drupal\rp_site_info;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Site info entity.
 *
 * @see \Drupal\rp_site_info\Entity\SiteInfo.
 */
class SiteInfoAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_site_info\Entity\SiteInfoInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished site info entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published site info entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit site info entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete site info entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add site info entities');
  }

}
