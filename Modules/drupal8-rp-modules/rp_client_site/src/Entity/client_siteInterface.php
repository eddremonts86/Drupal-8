<?php

namespace Drupal\rp_client_site\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Client_site entities.
 *
 * @ingroup rp_client_site
 */
interface client_siteInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Client_site name.
   *
   * @return string
   *   Name of the Client_site.
   */
  public function getName();

  /**
   * Sets the Client_site name.
   *
   * @param string $name
   *   The Client_site name.
   *
   * @return \Drupal\rp_client_site\Entity\client_siteInterface
   *   The called Client_site entity.
   */
  public function setName($name);

  /**
   * Gets the Client_site creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Client_site.
   */
  public function getCreatedTime();

  /**
   * Sets the Client_site creation timestamp.
   *
   * @param int $timestamp
   *   The Client_site creation timestamp.
   *
   * @return \Drupal\rp_client_site\Entity\client_siteInterface
   *   The called Client_site entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Client_site published status indicator.
   *
   * Unpublished Client_site are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Client_site is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Client_site.
   *
   * @param bool $published
   *   TRUE to set this Client_site to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_client_site\Entity\client_siteInterface
   *   The called Client_site entity.
   */
  public function setPublished($published);

  /**
   * Gets the Client_site revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Client_site revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_client_site\Entity\client_siteInterface
   *   The called Client_site entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Client_site revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Client_site revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_client_site\Entity\client_siteInterface
   *   The called Client_site entity.
   */
  public function setRevisionUserId($uid);

}
