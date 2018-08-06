<?php

namespace Drupal\rp_channel\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Channel entity.
 *
 * @ingroup rp_channel
 *
 * @ContentEntityType(
 *   id = "channel",
 *   label = @Translation("Channel"),
 *   handlers = {
 *     "storage" = "Drupal\rp_channel\ChannelStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\rp_channel\ChannelListBuilder",
 *     "views_data" = "Drupal\rp_channel\Entity\ChannelViewsData",
 *     "translation" = "Drupal\rp_channel\ChannelTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\rp_channel\Form\ChannelForm",
 *       "add" = "Drupal\rp_channel\Form\ChannelForm",
 *       "edit" = "Drupal\rp_channel\Form\ChannelForm",
 *       "delete" = "Drupal\rp_channel\Form\ChannelDeleteForm",
 *     },
 *     "access" = "Drupal\rp_channel\ChannelAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\rp_channel\ChannelHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "channel",
 *   data_table = "channel_field_data",
 *   revision_table = "channel_revision",
 *   revision_data_table = "channel_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer channel entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/rp/entity-content/channel/{channel}",
 *     "add-form" = "/admin/rp/entity-content/channel/add",
 *     "edit-form" = "/admin/rp/entity-content/channel/{channel}/edit",
 *     "delete-form" = "/admin/rp/entity-content/channel/{channel}/delete",
 *     "version-history" = "/admin/rp/entity-content/channel/{channel}/revisions",
 *     "revision" = "/admin/rp/entity-content/channel/{channel}/revisions/{channel_revision}/view",
 *     "revision_revert" = "/admin/rp/entity-content/channel/{channel}/revisions/{channel_revision}/revert",
 *     "translation_revert" = "/admin/rp/entity-content/channel/{channel}/revisions/{channel_revision}/revert/{langcode}",
 *     "revision_delete" = "/admin/rp/entity-content/channel/{channel}/revisions/{channel_revision}/delete",
 *     "collection" = "/admin/rp/entity-content/channel",
 *   },
 *   field_ui_base_route = "channel.settings"
 * )
 */
class Channel extends RevisionableContentEntityBase implements ChannelInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the channel owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Channel entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Channel entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Channel is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

  public function label() {
    if(!is_null($this->getApiCode()))
      return $this->getApiCode();
    else
      return parent::label();
  }

  /**
   * Get API Code
   */
  public function getApiCode() {
    if($this->hasField('field_channel_code'))
      return $this->get('field_channel_code')->value;
    else
      return null;
  }

  /**
   * Get API ID value
   */
  public function getApiId() {
    if($this->hasField('field_channel_api_id'))
      return $this->get('field_channel_api_id')->value;
    else
      return null;
  }

  /**
   * Get API Code Field
   */
  public function getFieldApiCode() {
    return 'field_channel_code';
  }

  /**
   * Get API ID Field
   */
  public function getFieldApiId() {
    return 'field_channel_api_id';
  }

  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    if ($rel === 'revision_revert' && $this instanceof RevisionableContentEntityBase) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableContentEntityBase) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    return $uri_route_parameters;
  }

}
