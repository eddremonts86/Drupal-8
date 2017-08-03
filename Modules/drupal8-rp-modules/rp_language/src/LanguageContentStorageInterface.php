<?php

namespace Drupal\rp_language;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_language\Entity\LanguageContentInterface;

/**
 * Defines the storage handler class for Language content entities.
 *
 * This extends the base storage class, adding required special handling for
 * Language content entities.
 *
 * @ingroup rp_language
 */
interface LanguageContentStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Language content revision IDs for a specific Language content.
   *
   * @param \Drupal\rp_language\Entity\LanguageContentInterface $entity
   *   The Language content entity.
   *
   * @return int[]
   *   Language content revision IDs (in ascending order).
   */
  public function revisionIds(LanguageContentInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Language content author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Language content revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_language\Entity\LanguageContentInterface $entity
   *   The Language content entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(LanguageContentInterface $entity);

  /**
   * Unsets the language for all Language content with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
