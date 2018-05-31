<?php

namespace Drupal\rp_stream_provider_info;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface;

/**
 * Defines the storage handler class for Stream Provider Info entities.
 *
 * This extends the base storage class, adding required special handling for
 * Stream Provider Info entities.
 *
 * @ingroup rp_stream_provider_info
 */
class StreamProviderInfoStorage extends SqlContentEntityStorage implements StreamProviderInfoStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(StreamProviderInfoInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {stream_provider_info_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {stream_provider_info_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(StreamProviderInfoInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {stream_provider_info_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('stream_provider_info_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
