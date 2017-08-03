<?php

namespace Drupal\rp_sites;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the site entity type.
 */
class SiteAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view rp_site');

      case 'update':
        return AccessResult::allowedIfHasPermissions($account, ['edit rp_site', 'administer rp_site'], 'OR');

      case 'delete':
        return AccessResult::allowedIfHasPermissions($account, ['delete rp_site', 'administer rp_site'], 'OR');

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, ['create rp_site', 'administer rp_site'], 'OR');
  }

}
