<?php

namespace Drupal\rp_language;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Language content entity.
 *
 * @see \Drupal\rp_language\Entity\LanguageContent.
 */
class LanguageContentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_language\Entity\LanguageContentInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished language content entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published language content entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit language content entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete language content entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add language content entities');
  }

}
