<?php

namespace Drupal\rp_game_info;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\rp_game_info\Entity\GameInfoInterface;

/**
 * Defines the storage handler class for Game info entities.
 *
 * This extends the base storage class, adding required special handling for
 * Game info entities.
 *
 * @ingroup rp_game_info
 */
interface GameInfoStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Game info revision IDs for a specific Game info.
   *
   * @param \Drupal\rp_game_info\Entity\GameInfoInterface $entity
   *   The Game info entity.
   *
   * @return int[]
   *   Game info revision IDs (in ascending order).
   */
  public function revisionIds(GameInfoInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Game info author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Game info revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\rp_game_info\Entity\GameInfoInterface $entity
   *   The Game info entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(GameInfoInterface $entity);

  /**
   * Unsets the language for all Game info with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
