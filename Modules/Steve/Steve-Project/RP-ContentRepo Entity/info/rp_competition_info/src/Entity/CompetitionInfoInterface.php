<?php

namespace Drupal\rp_competition_info\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Competition Info entities.
 *
 * @ingroup rp_competition_info
 */
interface CompetitionInfoInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Competition Info name.
   *
   * @return string
   *   Name of the Competition Info.
   */
  public function getName();

  /**
   * Sets the Competition Info name.
   *
   * @param string $name
   *   The Competition Info name.
   *
   * @return \Drupal\rp_competition_info\Entity\CompetitionInfoInterface
   *   The called Competition Info entity.
   */
  public function setName($name);

  /**
   * Gets the Competition Info creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Competition Info.
   */
  public function getCreatedTime();

  /**
   * Sets the Competition Info creation timestamp.
   *
   * @param int $timestamp
   *   The Competition Info creation timestamp.
   *
   * @return \Drupal\rp_competition_info\Entity\CompetitionInfoInterface
   *   The called Competition Info entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Competition Info published status indicator.
   *
   * Unpublished Competition Info are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Competition Info is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Competition Info.
   *
   * @param bool $published
   *   TRUE to set this Competition Info to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_competition_info\Entity\CompetitionInfoInterface
   *   The called Competition Info entity.
   */
  public function setPublished($published);

  /**
   * Gets the Competition Info revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Competition Info revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_competition_info\Entity\CompetitionInfoInterface
   *   The called Competition Info entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Competition Info revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Competition Info revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_competition_info\Entity\CompetitionInfoInterface
   *   The called Competition Info entity.
   */
  public function setRevisionUserId($uid);

}
