<?php

namespace Drupal\rp_site_info\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Site info entities.
 *
 * @ingroup rp_site_info
 */
interface SiteInfoInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Site info name.
   *
   * @return string
   *   Name of the Site info.
   */
  public function getName();

  /**
   * Sets the Site info name.
   *
   * @param string $name
   *   The Site info name.
   *
   * @return \Drupal\rp_site_info\Entity\SiteInfoInterface
   *   The called Site info entity.
   */
  public function setName($name);

  /**
   * Gets the Site info creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Site info.
   */
  public function getCreatedTime();

  /**
   * Sets the Site info creation timestamp.
   *
   * @param int $timestamp
   *   The Site info creation timestamp.
   *
   * @return \Drupal\rp_site_info\Entity\SiteInfoInterface
   *   The called Site info entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Site info published status indicator.
   *
   * Unpublished Site info are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Site info is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Site info.
   *
   * @param bool $published
   *   TRUE to set this Site info to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_site_info\Entity\SiteInfoInterface
   *   The called Site info entity.
   */
  public function setPublished($published);

  /**
   * Gets the Site info revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Site info revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_site_info\Entity\SiteInfoInterface
   *   The called Site info entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Site info revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Site info revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_site_info\Entity\SiteInfoInterface
   *   The called Site info entity.
   */
  public function setRevisionUserId($uid);

}
