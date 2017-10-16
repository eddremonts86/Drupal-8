<?php

namespace Drupal\rp_language;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_language\Entity\LanguageContentInterface;

/**
 * Defines the storage handler class for Language content entities.
 *
 * This extends the base storage class, adding required special handling for
 * Language content entities.
 *
 * @ingroup rp_language
 */
class LanguageContentStorage extends SqlContentEntityStorage implements LanguageContentStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(LanguageContentInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {language_content_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {language_content_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(LanguageContentInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {language_content_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('language_content_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
