<?php

namespace Drupal\rp_game;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_game\Entity\GameInterface;

/**
 * Defines the storage handler class for Game entities.
 *
 * This extends the base storage class, adding required special handling for
 * Game entities.
 *
 * @ingroup rp_game
 */
class GameStorage extends SqlContentEntityStorage implements GameStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(GameInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {game_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {game_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(GameInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {game_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('game_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
