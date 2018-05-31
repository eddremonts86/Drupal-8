<?php

namespace Drupal\rp_competition_info\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Competition Info entity.
 *
 * @ingroup rp_competition_info
 *
 * @ContentEntityType(
 *   id = "competition_info",
 *   label = @Translation("Competition Info"),
 *   handlers = {
 *     "storage" = "Drupal\rp_competition_info\CompetitionInfoStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\rp_competition_info\CompetitionInfoListBuilder",
 *     "views_data" = "Drupal\rp_competition_info\Entity\CompetitionInfoViewsData",
 *     "translation" = "Drupal\rp_competition_info\CompetitionInfoTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\rp_competition_info\Form\CompetitionInfoForm",
 *       "add" = "Drupal\rp_competition_info\Form\CompetitionInfoForm",
 *       "edit" = "Drupal\rp_competition_info\Form\CompetitionInfoForm",
 *       "delete" = "Drupal\rp_competition_info\Form\CompetitionInfoDeleteForm",
 *     },
 *     "access" = "Drupal\rp_competition_info\CompetitionInfoAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\rp_competition_info\CompetitionInfoHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "competition_info",
 *   data_table = "competition_info_field_data",
 *   revision_table = "competition_info_revision",
 *   revision_data_table = "competition_info_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer competition info entities",
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
 *     "canonical" = "/admin/rp/entity-content/competition_info/{competition_info}",
 *     "add-form" = "/admin/rp/entity-content/competition_info/add",
 *     "edit-form" = "/admin/rp/entity-content/competition_info/{competition_info}/edit",
 *     "delete-form" = "/admin/rp/entity-content/competition_info/{competition_info}/delete",
 *     "version-history" = "/admin/rp/entity-content/competition_info/{competition_info}/revisions",
 *     "revision" = "/admin/rp/entity-content/competition_info/{competition_info}/revisions/{competition_info_revision}/view",
 *     "revision_revert" = "/admin/rp/entity-content/competition_info/{competition_info}/revisions/{competition_info_revision}/revert",
 *     "translation_revert" = "/admin/rp/entity-content/competition_info/{competition_info}/revisions/{competition_info_revision}/revert/{langcode}",
 *     "revision_delete" = "/admin/rp/entity-content/competition_info/{competition_info}/revisions/{competition_info_revision}/delete",
 *     "collection" = "/admin/rp/entity-content/competition_info",
 *   },
 *   field_ui_base_route = "competition_info.settings"
 * )
 */
class CompetitionInfo extends RevisionableContentEntityBase implements CompetitionInfoInterface {

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

    // If no revision author has been set explicitly, make the competition_info owner the
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
      ->setDescription(t('The user ID of author of the Competition Info entity.'))
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
      ->setDescription(t('The name of the Competition Info entity.'))
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
      ->setDescription(t('A boolean indicating whether the Competition Info is published.'))
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

}
