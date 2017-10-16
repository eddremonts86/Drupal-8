<?php

namespace Drupal\rp_stream_provider;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_stream_provider\Entity\StreamProviderInterface;

/**
 * Defines the storage handler class for Stream Provider entities.
 *
 * This extends the base storage class, adding required special handling for
 * Stream Provider entities.
 *
 * @ingroup rp_stream_provider
 */
interface StreamProviderStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Stream Provider revision IDs for a specific Stream Provider.
   *
   * @param \Drupal\rp_stream_provider\Entity\StreamProviderInterface $entity
   *   The Stream Provider entity.
   *
   * @return int[]
   *   Stream Provider revision IDs (in ascending order).
   */
  public function revisionIds(StreamProviderInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Stream Provider author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Stream Provider revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_stream_provider\Entity\StreamProviderInterface $entity
   *   The Stream Provider entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(StreamProviderInterface $entity);

  /**
   * Unsets the language for all Stream Provider with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
