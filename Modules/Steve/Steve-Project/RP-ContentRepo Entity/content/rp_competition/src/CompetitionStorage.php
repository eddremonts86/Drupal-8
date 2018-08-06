<?php

namespace Drupal\rp_competition;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_competition\Entity\CompetitionInterface;

/**
 * Defines the storage handler class for Competition entities.
 *
 * This extends the base storage class, adding required special handling for
 * Competition entities.
 *
 * @ingroup rp_competition
 */
class CompetitionStorage extends SqlContentEntityStorage implements CompetitionStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(CompetitionInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {competition_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {competition_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(CompetitionInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {competition_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('competition_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
