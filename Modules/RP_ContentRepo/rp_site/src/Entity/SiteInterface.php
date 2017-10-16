<?php

namespace Drupal\rp_site\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Site entities.
 *
 * @ingroup rp_site
 */
interface SiteInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Site name.
   *
   * @return string
   *   Name of the Site.
   */
  public function getName();

  /**
   * Sets the Site name.
   *
   * @param string $name
   *   The Site name.
   *
   * @return \Drupal\rp_site\Entity\SiteInterface
   *   The called Site entity.
   */
  public function setName($name);

  /**
   * Gets the Site creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Site.
   */
  public function getCreatedTime();

  /**
   * Sets the Site creation timestamp.
   *
   * @param int $timestamp
   *   The Site creation timestamp.
   *
   * @return \Drupal\rp_site\Entity\SiteInterface
   *   The called Site entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Site published status indicator.
   *
   * Unpublished Site are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Site is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Site.
   *
   * @param bool $published
   *   TRUE to set this Site to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_site\Entity\SiteInterface
   *   The called Site entity.
   */
  public function setPublished($published);

  /**
   * Gets the Site revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Site revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_site\Entity\SiteInterface
   *   The called Site entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Site revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Site revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_site\Entity\SiteInterface
   *   The called Site entity.
   */
  public function setRevisionUserId($uid);

}
