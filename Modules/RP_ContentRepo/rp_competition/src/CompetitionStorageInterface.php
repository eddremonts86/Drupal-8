<?php

namespace Drupal\rp_competition;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface CompetitionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Competition revision IDs for a specific Competition.
   *
   * @param \Drupal\rp_competition\Entity\CompetitionInterface $entity
   *   The Competition entity.
   *
   * @return int[]
   *   Competition revision IDs (in ascending order).
   */
  public function revisionIds(CompetitionInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Competition author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Competition revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_competition\Entity\CompetitionInterface $entity
   *   The Competition entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CompetitionInterface $entity);

  /**
   * Unsets the language for all Competition with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
