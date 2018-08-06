<?php

namespace Drupal\rp_participant_info;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Participant Info entity.
 *
 * @see \Drupal\rp_participant_info\Entity\ParticipantInfo.
 */
class ParticipantInfoAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rp_participant_info\Entity\ParticipantInfoInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished participant info entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published participant info entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit participant info entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete participant info entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add participant info entities');
  }

}
