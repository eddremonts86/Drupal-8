<?php

namespace Drupal\rp_client_site;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_client_site\Entity\client_siteInterface;

/**
 * Defines the storage handler class for Client_site entities.
 *
 * This extends the base storage class, adding required special handling for
 * Client_site entities.
 *
 * @ingroup rp_client_site
 */
interface client_siteStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Client_site revision IDs for a specific Client_site.
   *
   * @param \Drupal\rp_client_site\Entity\client_siteInterface $entity
   *   The Client_site entity.
   *
   * @return int[]
   *   Client_site revision IDs (in ascending order).
   */
  public function revisionIds(client_siteInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Client_site author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Client_site revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_client_site\Entity\client_siteInterface $entity
   *   The Client_site entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(client_siteInterface $entity);

  /**
   * Unsets the language for all Client_site with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
