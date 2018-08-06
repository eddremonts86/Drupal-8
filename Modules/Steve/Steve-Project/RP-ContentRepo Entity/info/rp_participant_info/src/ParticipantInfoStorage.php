<?php

namespace Drupal\rp_participant_info;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_participant_info\Entity\ParticipantInfoInterface;

/**
 * Defines the storage handler class for Participant Info entities.
 *
 * This extends the base storage class, adding required special handling for
 * Participant Info entities.
 *
 * @ingroup rp_participant_info
 */
class ParticipantInfoStorage extends SqlContentEntityStorage implements ParticipantInfoStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ParticipantInfoInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {participant_info_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {participant_info_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ParticipantInfoInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {participant_info_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('participant_info_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
