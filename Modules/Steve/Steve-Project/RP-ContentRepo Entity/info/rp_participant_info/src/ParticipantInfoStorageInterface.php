<?php

namespace Drupal\rp_participant_info;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_participant_info\Entity\ParticipantInfoInterface;

/**
 * Defines the storage handler class for Participant Info entities.
 *
 * This extends the base storage class, adding required special handling for
 * Participant Info entities.
 *
 * @ingroup rp_participant_info
 */
interface ParticipantInfoStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Participant Info revision IDs for a specific Participant Info.
   *
   * @param \Drupal\rp_participant_info\Entity\ParticipantInfoInterface $entity
   *   The Participant Info entity.
   *
   * @return int[]
   *   Participant Info revision IDs (in ascending order).
   */
  public function revisionIds(ParticipantInfoInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Participant Info author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Participant Info revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_participant_info\Entity\ParticipantInfoInterface $entity
   *   The Participant Info entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ParticipantInfoInterface $entity);

  /**
   * Unsets the language for all Participant Info with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
