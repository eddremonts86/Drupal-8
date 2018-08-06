<?php

namespace Drupal\rp_competition_info;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_competition_info\Entity\CompetitionInfoInterface;

/**
 * Defines the storage handler class for Competition Info entities.
 *
 * This extends the base storage class, adding required special handling for
 * Competition Info entities.
 *
 * @ingroup rp_competition_info
 */
class CompetitionInfoStorage extends SqlContentEntityStorage implements CompetitionInfoStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(CompetitionInfoInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {competition_info_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {competition_info_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(CompetitionInfoInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {competition_info_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('competition_info_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
