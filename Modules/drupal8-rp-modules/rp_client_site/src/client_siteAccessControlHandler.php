<?php

namespace Drupal\rp_client_site;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Client_site entity.
 *
 * @see \Drupal\rp_client_site\Entity\client_site.
 */
class client_siteAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_client_site\Entity\client_siteInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished client_site entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published client_site entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit client_site entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete client_site entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add client_site entities');
  }

}
