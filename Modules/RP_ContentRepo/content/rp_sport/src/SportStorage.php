<?php

namespace Drupal\rp_sport;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_sport\Entity\SportInterface;

/**
 * Defines the storage handler class for Sport entities.
 *
 * This extends the base storage class, adding required special handling for
 * Sport entities.
 *
 * @ingroup rp_sport
 */
class SportStorage extends SqlContentEntityStorage implements SportStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(SportInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {sport_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {sport_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(SportInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {sport_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('sport_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
