<?php

namespace Drupal\rp_channel;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_channel\Entity\ChannelInterface;

/**
 * Defines the storage handler class for Channel entities.
 *
 * This extends the base storage class, adding required special handling for
 * Channel entities.
 *
 * @ingroup rp_channel
 */
class ChannelStorage extends SqlContentEntityStorage implements ChannelStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ChannelInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {channel_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {channel_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ChannelInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {channel_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('channel_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
