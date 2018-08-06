<?php

namespace Drupal\rp_competition_info;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface CompetitionInfoStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Competition Info revision IDs for a specific Competition Info.
   *
   * @param \Drupal\rp_competition_info\Entity\CompetitionInfoInterface $entity
   *   The Competition Info entity.
   *
   * @return int[]
   *   Competition Info revision IDs (in ascending order).
   */
  public function revisionIds(CompetitionInfoInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Competition Info author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Competition Info revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_competition_info\Entity\CompetitionInfoInterface $entity
   *   The Competition Info entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CompetitionInfoInterface $entity);

  /**
   * Unsets the language for all Competition Info with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
