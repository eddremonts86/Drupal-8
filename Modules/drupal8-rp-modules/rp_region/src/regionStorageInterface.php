<?php

namespace Drupal\rp_region;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface regionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Region revision IDs for a specific Region.
   *
   * @param \Drupal\rp_region\Entity\regionInterface $entity
   *   The Region entity.
   *
   * @return int[]
   *   Region revision IDs (in ascending order).
   */
  public function revisionIds(regionInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Region author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Region revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_region\Entity\regionInterface $entity
   *   The Region entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(regionInterface $entity);

  /**
   * Unsets the language for all Region with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
