<?php

namespace Drupal\rp_language\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Language content entities.
 *
 * @ingroup rp_language
 */
interface LanguageContentInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Language content name.
   *
   * @return string
   *   Name of the Language content.
   */
  public function getName();

  /**
   * Sets the Language content name.
   *
   * @param string $name
   *   The Language content name.
   *
   * @return \Drupal\rp_language\Entity\LanguageContentInterface
   *   The called Language content entity.
   */
  public function setName($name);

  /**
   * Gets the Language content creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Language content.
   */
  public function getCreatedTime();

  /**
   * Sets the Language content creation timestamp.
   *
   * @param int $timestamp
   *   The Language content creation timestamp.
   *
   * @return \Drupal\rp_language\Entity\LanguageContentInterface
   *   The called Language content entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Language content published status indicator.
   *
   * Unpublished Language content are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Language content is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Language content.
   *
   * @param bool $published
   *   TRUE to set this Language content to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_language\Entity\LanguageContentInterface
   *   The called Language content entity.
   */
  public function setPublished($published);

  /**
   * Gets the Language content revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Language content revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_language\Entity\LanguageContentInterface
   *   The called Language content entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Language content revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Language content revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_language\Entity\LanguageContentInterface
   *   The called Language content entity.
   */
  public function setRevisionUserId($uid);

}
