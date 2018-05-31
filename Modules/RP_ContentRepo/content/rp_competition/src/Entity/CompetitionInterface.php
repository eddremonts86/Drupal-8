<?php

namespace Drupal\rp_competition\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Competition entities.
 *
 * @ingroup rp_competition
 */
interface CompetitionInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Competition name.
   *
   * @return string
   *   Name of the Competition.
   */
  public function getName();

  /**
   * Sets the Competition name.
   *
   * @param string $name
   *   The Competition name.
   *
   * @return \Drupal\rp_competition\Entity\CompetitionInterface
   *   The called Competition entity.
   */
  public function setName($name);

  /**
   * Gets the Competition creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Competition.
   */
  public function getCreatedTime();

  /**
   * Sets the Competition creation timestamp.
   *
   * @param int $timestamp
   *   The Competition creation timestamp.
   *
   * @return \Drupal\rp_competition\Entity\CompetitionInterface
   *   The called Competition entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Competition published status indicator.
   *
   * Unpublished Competition are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Competition is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Competition.
   *
   * @param bool $published
   *   TRUE to set this Competition to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_competition\Entity\CompetitionInterface
   *   The called Competition entity.
   */
  public function setPublished($published);

  /**
   * Gets the Competition revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Competition revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_competition\Entity\CompetitionInterface
   *   The called Competition entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Competition revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Competition revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_competition\Entity\CompetitionInterface
   *   The called Competition entity.
   */
  public function setRevisionUserId($uid);

}
