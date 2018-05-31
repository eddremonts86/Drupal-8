<?php

namespace Drupal\rp_sport_info;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_sport_info\Entity\SportInfoInterface;

/**
 * Defines the storage handler class for Sport info entities.
 *
 * This extends the base storage class, adding required special handling for
 * Sport info entities.
 *
 * @ingroup rp_sport_info
 */
interface SportInfoStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Sport info revision IDs for a specific Sport info.
   *
   * @param \Drupal\rp_sport_info\Entity\SportInfoInterface $entity
   *   The Sport info entity.
   *
   * @return int[]
   *   Sport info revision IDs (in ascending order).
   */
  public function revisionIds(SportInfoInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Sport info author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Sport info revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_sport_info\Entity\SportInfoInterface $entity
   *   The Sport info entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(SportInfoInterface $entity);

  /**
   * Unsets the language for all Sport info with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
