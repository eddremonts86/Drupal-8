<?php

namespace Drupal\rp_cms_channels_by_contenttype\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Channels_by_contenttype entities.
 *
 * @ingroup rp_cms_channels_by_contenttype
 */
interface channels_by_contenttypeInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Channels_by_contenttype name.
   *
   * @return string
   *   Name of the Channels_by_contenttype.
   */
  public function getName();

  /**
   * Sets the Channels_by_contenttype name.
   *
   * @param string $name
   *   The Channels_by_contenttype name.
   *
   * @return \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface
   *   The called Channels_by_contenttype entity.
   */
  public function setName($name);

  /**
   * Gets the Channels_by_contenttype creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Channels_by_contenttype.
   */
  public function getCreatedTime();

  /**
   * Sets the Channels_by_contenttype creation timestamp.
   *
   * @param int $timestamp
   *   The Channels_by_contenttype creation timestamp.
   *
   * @return \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface
   *   The called Channels_by_contenttype entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Channels_by_contenttype published status indicator.
   *
   * Unpublished Channels_by_contenttype are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Channels_by_contenttype is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Channels_by_contenttype.
   *
   * @param bool $published
   *   TRUE to set this Channels_by_contenttype to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface
   *   The called Channels_by_contenttype entity.
   */
  public function setPublished($published);

  /**
   * Gets the Channels_by_contenttype revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Channels_by_contenttype revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface
   *   The called Channels_by_contenttype entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Channels_by_contenttype revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Channels_by_contenttype revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface
   *   The called Channels_by_contenttype entity.
   */
  public function setRevisionUserId($uid);

}
