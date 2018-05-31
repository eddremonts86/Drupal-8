<?php

namespace Drupal\rp_stream_provider\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Stream Provider entities.
 *
 * @ingroup rp_stream_provider
 */
interface StreamProviderInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Stream Provider name.
   *
   * @return string
   *   Name of the Stream Provider.
   */
  public function getName();

  /**
   * Sets the Stream Provider name.
   *
   * @param string $name
   *   The Stream Provider name.
   *
   * @return \Drupal\rp_stream_provider\Entity\StreamProviderInterface
   *   The called Stream Provider entity.
   */
  public function setName($name);

  /**
   * Gets the Stream Provider creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Stream Provider.
   */
  public function getCreatedTime();

  /**
   * Sets the Stream Provider creation timestamp.
   *
   * @param int $timestamp
   *   The Stream Provider creation timestamp.
   *
   * @return \Drupal\rp_stream_provider\Entity\StreamProviderInterface
   *   The called Stream Provider entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Stream Provider published status indicator.
   *
   * Unpublished Stream Provider are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Stream Provider is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Stream Provider.
   *
   * @param bool $published
   *   TRUE to set this Stream Provider to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_stream_provider\Entity\StreamProviderInterface
   *   The called Stream Provider entity.
   */
  public function setPublished($published);

  /**
   * Gets the Stream Provider revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Stream Provider revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_stream_provider\Entity\StreamProviderInterface
   *   The called Stream Provider entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Stream Provider revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Stream Provider revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_stream_provider\Entity\StreamProviderInterface
   *   The called Stream Provider entity.
   */
  public function setRevisionUserId($uid);

}
