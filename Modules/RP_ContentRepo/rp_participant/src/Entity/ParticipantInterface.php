<?php

namespace Drupal\rp_participant\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Participant entities.
 *
 * @ingroup rp_participant
 */
interface ParticipantInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Participant name.
   *
   * @return string
   *   Name of the Participant.
   */
  public function getName();

  /**
   * Sets the Participant name.
   *
   * @param string $name
   *   The Participant name.
   *
   * @return \Drupal\rp_participant\Entity\ParticipantInterface
   *   The called Participant entity.
   */
  public function setName($name);

  /**
   * Gets the Participant creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Participant.
   */
  public function getCreatedTime();

  /**
   * Sets the Participant creation timestamp.
   *
   * @param int $timestamp
   *   The Participant creation timestamp.
   *
   * @return \Drupal\rp_participant\Entity\ParticipantInterface
   *   The called Participant entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Participant published status indicator.
   *
   * Unpublished Participant are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Participant is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Participant.
   *
   * @param bool $published
   *   TRUE to set this Participant to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rp_participant\Entity\ParticipantInterface
   *   The called Participant entity.
   */
  public function setPublished($published);

  /**
   * Gets the Participant revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Participant revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\rp_participant\Entity\ParticipantInterface
   *   The called Participant entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Participant revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Participant revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\rp_participant\Entity\ParticipantInterface
   *   The called Participant entity.
   */
  public function setRevisionUserId($uid);

}
