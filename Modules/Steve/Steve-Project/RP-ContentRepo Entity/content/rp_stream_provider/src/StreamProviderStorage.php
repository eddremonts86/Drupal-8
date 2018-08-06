<?php

namespace Drupal\rp_stream_provider;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_stream_provider\Entity\StreamProviderInterface;

/**
 * Defines the storage handler class for Stream Provider entities.
 *
 * This extends the base storage class, adding required special handling for
 * Stream Provider entities.
 *
 * @ingroup rp_stream_provider
 */
class StreamProviderStorage extends SqlContentEntityStorage implements StreamProviderStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(StreamProviderInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {stream_provider_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {stream_provider_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(StreamProviderInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {stream_provider_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('stream_provider_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
