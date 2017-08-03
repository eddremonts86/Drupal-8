<?php

namespace Drupal\rp_channel;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_channel\Entity\ChannelInterface;

/**
 * Defines the storage handler class for Channel entities.
 *
 * This extends the base storage class, adding required special handling for
 * Channel entities.
 *
 * @ingroup rp_channel
 */
interface ChannelStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Channel revision IDs for a specific Channel.
   *
   * @param \Drupal\rp_channel\Entity\ChannelInterface $entity
   *   The Channel entity.
   *
   * @return int[]
   *   Channel revision IDs (in ascending order).
   */
  public function revisionIds(ChannelInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Channel author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Channel revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_channel\Entity\ChannelInterface $entity
   *   The Channel entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ChannelInterface $entity);

  /**
   * Unsets the language for all Channel with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
