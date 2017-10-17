<?php

namespace Drupal\rp_game_info;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_game_info\Entity\GameInfoInterface;

/**
 * Defines the storage handler class for Game info entities.
 *
 * This extends the base storage class, adding required special handling for
 * Game info entities.
 *
 * @ingroup rp_game_info
 */
class GameInfoStorage extends SqlContentEntityStorage implements GameInfoStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(GameInfoInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {game_info_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {game_info_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(GameInfoInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {game_info_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('game_info_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
