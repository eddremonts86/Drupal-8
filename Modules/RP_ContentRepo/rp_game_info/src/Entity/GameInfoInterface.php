<?php

namespace Drupal\rp_game_info\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Game info entities.
 *
 * @ingroup rp_game_info
 */
interface GameInfoInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Game info name.
   *
   * @return string
   *   Name of the Game info.
   */
  public function getName();

  /**
   * Sets the Game info name.
   *
   * @param string $name
   *   The Game info name.
   *
   * @return \Drupal\rp_game_info\Entity\GameInfoInterface
   *   The called Game info entity.
   */
  public function setName($name);

  /**
   * Gets the Game info creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Game info.
   */
  public function getCreatedTime();

  /**
   * Sets the Game info creation timestamp.
   *
   * @param int $timestamp
   *   The Game info creation timestamp.
   *
   * @return \Drupal\rp_game_info\Entity\GameInfoInterface
   *   The called Game info entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Game info published status indicator.
   *
   * Unpublished Game info are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Game info is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Game info.
   *
   * @param bool $published
   *   TRUE to set this Game info to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_game_info\Entity\GameInfoInterface
   *   The called Game info entity.
   */
  public function setPublished($published);

  /**
   * Gets the Game info revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Game info revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_game_info\Entity\GameInfoInterface
   *   The called Game info entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Game info revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Game info revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_game_info\Entity\GameInfoInterface
   *   The called Game info entity.
   */
  public function setRevisionUserId($uid);

}
