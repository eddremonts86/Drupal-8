<?php

namespace Drupal\rp_sport_info\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Sport info entities.
 *
 * @ingroup rp_sport_info
 */
interface SportInfoInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Sport info name.
   *
   * @return string
   *   Name of the Sport info.
   */
  public function getName();

  /**
   * Sets the Sport info name.
   *
   * @param string $name
   *   The Sport info name.
   *
   * @return \Drupal\rp_sport_info\Entity\SportInfoInterface
   *   The called Sport info entity.
   */
  public function setName($name);

  /**
   * Gets the Sport info creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Sport info.
   */
  public function getCreatedTime();

  /**
   * Sets the Sport info creation timestamp.
   *
   * @param int $timestamp
   *   The Sport info creation timestamp.
   *
   * @return \Drupal\rp_sport_info\Entity\SportInfoInterface
   *   The called Sport info entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Sport info published status indicator.
   *
   * Unpublished Sport info are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Sport info is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Sport info.
   *
   * @param bool $published
   *   TRUE to set this Sport info to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_sport_info\Entity\SportInfoInterface
   *   The called Sport info entity.
   */
  public function setPublished($published);

  /**
   * Gets the Sport info revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Sport info revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_sport_info\Entity\SportInfoInterface
   *   The called Sport info entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Sport info revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Sport info revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_sport_info\Entity\SportInfoInterface
   *   The called Sport info entity.
   */
  public function setRevisionUserId($uid);

}
