<?php

namespace Drupal\rp_region;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_region\Entity\regionInterface;

/**
 * Defines the storage handler class for Region entities.
 *
 * This extends the base storage class, adding required special handling for
 * Region entities.
 *
 * @ingroup rp_region
 */
class regionStorage extends SqlContentEntityStorage implements regionStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(regionInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {region_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {region_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(regionInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {region_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('region_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
