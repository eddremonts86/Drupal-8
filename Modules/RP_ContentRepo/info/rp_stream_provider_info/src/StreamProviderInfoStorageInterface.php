<?php

namespace Drupal\rp_stream_provider_info;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface;

/**
 * Defines the storage handler class for Stream Provider Info entities.
 *
 * This extends the base storage class, adding required special handling for
 * Stream Provider Info entities.
 *
 * @ingroup rp_stream_provider_info
 */
interface StreamProviderInfoStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Stream Provider Info revision IDs for a specific Stream Provider Info.
   *
   * @param \Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface $entity
   *   The Stream Provider Info entity.
   *
   * @return int[]
   *   Stream Provider Info revision IDs (in ascending order).
   */
  public function revisionIds(StreamProviderInfoInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Stream Provider Info author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Stream Provider Info revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface $entity
   *   The Stream Provider Info entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(StreamProviderInfoInterface $entity);

  /**
   * Unsets the language for all Stream Provider Info with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
