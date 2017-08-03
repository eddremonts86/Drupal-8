<?php

namespace Drupal\rp_region\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Region entities.
 *
 * @ingroup rp_region
 */
interface regionInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Region name.
   *
   * @return string
   *   Name of the Region.
   */
  public function getName();

  /**
   * Sets the Region name.
   *
   * @param string $name
   *   The Region name.
   *
   * @return \Drupal\rp_region\Entity\regionInterface
   *   The called Region entity.
   */
  public function setName($name);

  /**
   * Gets the Region creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Region.
   */
  public function getCreatedTime();

  /**
   * Sets the Region creation timestamp.
   *
   * @param int $timestamp
   *   The Region creation timestamp.
   *
   * @return \Drupal\rp_region\Entity\regionInterface
   *   The called Region entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Region published status indicator.
   *
   * Unpublished Region are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Region is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Region.
   *
   * @param bool $published
   *   TRUE to set this Region to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_region\Entity\regionInterface
   *   The called Region entity.
   */
  public function setPublished($published);

  /**
   * Gets the Region revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Region revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_region\Entity\regionInterface
   *   The called Region entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Region revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Region revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_region\Entity\regionInterface
   *   The called Region entity.
   */
  public function setRevisionUserId($uid);

}
