<?php

namespace Drupal\rp_participant;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_participant\Entity\ParticipantInterface;

/**
 * Defines the storage handler class for Participant entities.
 *
 * This extends the base storage class, adding required special handling for
 * Participant entities.
 *
 * @ingroup rp_participant
 */
class ParticipantStorage extends SqlContentEntityStorage implements ParticipantStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ParticipantInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {participant_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {participant_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ParticipantInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {participant_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('participant_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
