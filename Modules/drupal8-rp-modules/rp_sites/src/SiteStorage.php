<?php

namespace Drupal\rp_sites;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_sites\SiteInterface;

/**
 * Defines the storage handler class for Site entities.
 *
 * This extends the base storage class, adding required special handling for
 * Site entities.
 *
 * @ingroup rp_sites
 */
class SiteStorage extends SqlContentEntityStorage implements SiteStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(SiteInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {rp_site_revision} WHERE id=:id ORDER BY vid',
      array(':id' => $entity->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {rp_site_field_revision} WHERE uid = :uid ORDER BY vid',
      array(':uid' => $account->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(SiteInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {rp_site_field_revision} WHERE id = :id AND default_langcode = 1', array(':id' => $entity->id()))
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('rp_site_revision')
      ->fields(array('langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED))
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
