<?php

namespace Drupal\rp_sport\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Sport entities.
 *
 * @ingroup rp_sport
 */
interface SportInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Sport name.
   *
   * @return string
   *   Name of the Sport.
   */
  public function getName();

  /**
   * Sets the Sport name.
   *
   * @param string $name
   *   The Sport name.
   *
   * @return \Drupal\rp_sport\Entity\SportInterface
   *   The called Sport entity.
   */
  public function setName($name);

  /**
   * Gets the Sport creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Sport.
   */
  public function getCreatedTime();

  /**
   * Sets the Sport creation timestamp.
   *
   * @param int $timestamp
   *   The Sport creation timestamp.
   *
   * @return \Drupal\rp_sport\Entity\SportInterface
   *   The called Sport entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Sport published status indicator.
   *
   * Unpublished Sport are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Sport is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Sport.
   *
   * @param bool $published
   *   TRUE to set this Sport to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_sport\Entity\SportInterface
   *   The called Sport entity.
   */
  public function setPublished($published);

  /**
   * Gets the Sport revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Sport revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_sport\Entity\SportInterface
   *   The called Sport entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Sport revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Sport revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_sport\Entity\SportInterface
   *   The called Sport entity.
   */
  public function setRevisionUserId($uid);

}
