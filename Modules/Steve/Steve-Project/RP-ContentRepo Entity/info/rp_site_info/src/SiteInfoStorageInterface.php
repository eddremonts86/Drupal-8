<?php

namespace Drupal\rp_site_info;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface SiteInfoStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Site info revision IDs for a specific Site info.
   *
   * @param \Drupal\rp_site_info\Entity\SiteInfoInterface $entity
   *   The Site info entity.
   *
   * @return int[]
   *   Site info revision IDs (in ascending order).
   */
  public function revisionIds(SiteInfoInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Site info author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Site info revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_site_info\Entity\SiteInfoInterface $entity
   *   The Site info entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(SiteInfoInterface $entity);

  /**
   * Unsets the language for all Site info with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
