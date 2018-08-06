<?php

namespace Drupal\rp_sport;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_sport\Entity\SportInterface;

/**
 * Defines the storage handler class for Sport entities.
 *
 * This extends the base storage class, adding required special handling for
 * Sport entities.
 *
 * @ingroup rp_sport
 */
interface SportStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Sport revision IDs for a specific Sport.
   *
   * @param \Drupal\rp_sport\Entity\SportInterface $entity
   *   The Sport entity.
   *
   * @return int[]
   *   Sport revision IDs (in ascending order).
   */
  public function revisionIds(SportInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Sport author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Sport revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_sport\Entity\SportInterface $entity
   *   The Sport entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(SportInterface $entity);

  /**
   * Unsets the language for all Sport with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
