<?php

namespace Drupal\rp_cms_channels_by_contenttype;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface;

/**
 * Defines the storage handler class for Channels_by_contenttype entities.
 *
 * This extends the base storage class, adding required special handling for
 * Channels_by_contenttype entities.
 *
 * @ingroup rp_cms_channels_by_contenttype
 */
class channels_by_contenttypeStorage extends SqlContentEntityStorage implements channels_by_contenttypeStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(channels_by_contenttypeInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {channels_by_contenttype_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {channels_by_contenttype_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(channels_by_contenttypeInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {channels_by_contenttype_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('channels_by_contenttype_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
