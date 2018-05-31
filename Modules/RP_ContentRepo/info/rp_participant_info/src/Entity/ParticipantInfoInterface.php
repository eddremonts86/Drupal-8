<?php

namespace Drupal\rp_participant_info\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Participant Info entities.
 *
 * @ingroup rp_participant_info
 */
interface ParticipantInfoInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Participant Info name.
   *
   * @return string
   *   Name of the Participant Info.
   */
  public function getName();

  /**
   * Sets the Participant Info name.
   *
   * @param string $name
   *   The Participant Info name.
   *
   * @return \Drupal\rp_participant_info\Entity\ParticipantInfoInterface
   *   The called Participant Info entity.
   */
  public function setName($name);

  /**
   * Gets the Participant Info creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Participant Info.
   */
  public function getCreatedTime();

  /**
   * Sets the Participant Info creation timestamp.
   *
   * @param int $timestamp
   *   The Participant Info creation timestamp.
   *
   * @return \Drupal\rp_participant_info\Entity\ParticipantInfoInterface
   *   The called Participant Info entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Participant Info published status indicator.
   *
   * Unpublished Participant Info are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Participant Info is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Participant Info.
   *
   * @param bool $published
   *   TRUE to set this Participant Info to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_participant_info\Entity\ParticipantInfoInterface
   *   The called Participant Info entity.
   */
  public function setPublished($published);

  /**
   * Gets the Participant Info revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Participant Info revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_participant_info\Entity\ParticipantInfoInterface
   *   The called Participant Info entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Participant Info revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Participant Info revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_participant_info\Entity\ParticipantInfoInterface
   *   The called Participant Info entity.
   */
  public function setRevisionUserId($uid);

}
