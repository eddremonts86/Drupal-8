<?php

namespace Drupal\rp_participant;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_participant\Entity\ParticipantInterface;

/**
 * Defines the storage handler class for Participant entities.
 *
 * This extends the base storage class, adding required special handling for
 * Participant entities.
 *
 * @ingroup rp_participant
 */
interface ParticipantStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Participant revision IDs for a specific Participant.
   *
   * @param \Drupal\rp_participant\Entity\ParticipantInterface $entity
   *   The Participant entity.
   *
   * @return int[]
   *   Participant revision IDs (in ascending order).
   */
  public function revisionIds(ParticipantInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Participant author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Participant revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_participant\Entity\ParticipantInterface $entity
   *   The Participant entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ParticipantInterface $entity);

  /**
   * Unsets the language for all Participant with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
