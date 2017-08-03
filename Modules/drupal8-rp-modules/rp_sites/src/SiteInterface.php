<?php

namespace Drupal\rp_sites;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a site entity type.
 */
interface SiteInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the site title.
   *
   * @return string
   *   Title of the site.
   */
  public function getTitle();

  /**
   * Sets the site title.
   *
   * @param string $title
   *   The site title.
   *
   * @return \Drupal\rp_sites\SiteInterface
   *   The called site entity.
   */
  public function setTitle($title);

  /**
   * Gets the site creation timestamp.
   *
   * @return int
   *   Creation timestamp of the site.
   */
  public function getCreatedTime();

  /**
   * Sets the site creation timestamp.
   *
   * @param int $timestamp
   *   The site creation timestamp.
   *
   * @return \Drupal\rp_sites\SiteInterface
   *   The called site entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the site status.
   *
   * @return bool
   *   TRUE if the site is enabled, FALSE othervise.
   */
  public function isEnabled();

  /**
   * Sets the site status.
   *
   * @param bool $status
   *   TRUE to enable this site, FALSE to disable.
   *
   * @return \Drupal\rp_sites\SiteInterface
   *   The called site entity.
   */
  public function setStatus($status);

  /**
   * Gets the site revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the site revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_sites\SiteInterface
   *   The called site entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the site revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the site revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_sites\SiteInterface
   *   The called site entity.
   */
  public function setRevisionUserId($uid);

}
