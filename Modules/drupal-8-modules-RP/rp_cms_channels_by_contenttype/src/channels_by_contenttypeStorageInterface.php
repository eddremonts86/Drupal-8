<?php

namespace Drupal\rp_cms_channels_by_contenttype;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface;

/**
 * Defines the storage handler class for Channels_by_contenttype entities.
 *
 * This extends the base storage class, adding required special handling for
 * Channels_by_contenttype entities.
 *
 * @ingroup rp_cms_channels_by_contenttype
 */
interface channels_by_contenttypeStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Channels_by_contenttype revision IDs for a specific Channels_by_contenttype.
   *
   * @param \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface $entity
   *   The Channels_by_contenttype entity.
   *
   * @return int[]
   *   Channels_by_contenttype revision IDs (in ascending order).
   */
  public function revisionIds(channels_by_contenttypeInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Channels_by_contenttype author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Channels_by_contenttype revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface $entity
   *   The Channels_by_contenttype entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(channels_by_contenttypeInterface $entity);

  /**
   * Unsets the language for all Channels_by_contenttype with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
