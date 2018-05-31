<?php

namespace Drupal\rp_site_info;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_site_info\Entity\SiteInfoInterface;

/**
 * Defines the storage handler class for Site info entities.
 *
 * This extends the base storage class, adding required special handling for
 * Site info entities.
 *
 * @ingroup rp_site_info
 */
class SiteInfoStorage extends SqlContentEntityStorage implements SiteInfoStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(SiteInfoInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {site_info_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {site_info_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(SiteInfoInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {site_info_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('site_info_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
