<?php

namespace Drupal\rp_stream_provider_info\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Stream Provider Info entities.
 *
 * @ingroup rp_stream_provider_info
 */
interface StreamProviderInfoInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Stream Provider Info name.
   *
   * @return string
   *   Name of the Stream Provider Info.
   */
  public function getName();

  /**
   * Sets the Stream Provider Info name.
   *
   * @param string $name
   *   The Stream Provider Info name.
   *
   * @return \Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface
   *   The called Stream Provider Info entity.
   */
  public function setName($name);

  /**
   * Gets the Stream Provider Info creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Stream Provider Info.
   */
  public function getCreatedTime();

  /**
   * Sets the Stream Provider Info creation timestamp.
   *
   * @param int $timestamp
   *   The Stream Provider Info creation timestamp.
   *
   * @return \Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface
   *   The called Stream Provider Info entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Stream Provider Info published status indicator.
   *
   * Unpublished Stream Provider Info are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Stream Provider Info is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Stream Provider Info.
   *
   * @param bool $published
   *   TRUE to set this Stream Provider Info to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface
   *   The called Stream Provider Info entity.
   */
  public function setPublished($published);

  /**
   * Gets the Stream Provider Info revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Stream Provider Info revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface
   *   The called Stream Provider Info entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Stream Provider Info revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Stream Provider Info revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface
   *   The called Stream Provider Info entity.
   */
  public function setRevisionUserId($uid);

}
