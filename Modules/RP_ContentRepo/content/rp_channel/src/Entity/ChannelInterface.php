<?php

namespace Drupal\rp_channel\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Channel entities.
 *
 * @ingroup rp_channel
 */
interface ChannelInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Channel name.
   *
   * @return string
   *   Name of the Channel.
   */
  public function getName();

  /**
   * Sets the Channel name.
   *
   * @param string $name
   *   The Channel name.
   *
   * @return \Drupal\rp_channel\Entity\ChannelInterface
   *   The called Channel entity.
   */
  public function setName($name);

  /**
   * Gets the Channel creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Channel.
   */
  public function getCreatedTime();

  /**
   * Sets the Channel creation timestamp.
   *
   * @param int $timestamp
   *   The Channel creation timestamp.
   *
   * @return \Drupal\rp_channel\Entity\ChannelInterface
   *   The called Channel entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Channel published status indicator.
   *
   * Unpublished Channel are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Channel is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Channel.
   *
   * @param bool $published
   *   TRUE to set this Channel to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_channel\Entity\ChannelInterface
   *   The called Channel entity.
   */
  public function setPublished($published);

  /**
   * Gets the Channel revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Channel revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_channel\Entity\ChannelInterface
   *   The called Channel entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Channel revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Channel revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_channel\Entity\ChannelInterface
   *   The called Channel entity.
   */
  public function setRevisionUserId($uid);

}
