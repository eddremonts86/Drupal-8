<?php

namespace Drupal\rp_site_info\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\rp_site_info\Entity\SiteInfo;
use Drupal\rp_site_info\Entity\SiteInfoConfig;

/**
 * Class SiteInfoConfigForm.
 *
 * @package Drupal\rp_site_info\Form
 */
class SiteInfoConfigForm extends EntityForm {

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected $fieldManager;

  /**
   * The entity type repository.
   *
   * @var \Drupal\Core\Entity\EntityTypeRepositoryInterface
   */
  protected $entityTypeRepository;

  /**
   * The current route match.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * SiteInfoConfigForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityFieldManager $field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeRepositoryInterface $entity_type_repository
   *   The entity type repository.
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityFieldManager $field_manager, EntityTypeRepositoryInterface $entity_type_repository, Request $request) {
    $this->entityManager = $entity_manager;
    $this->fieldManager = $field_manager;
    $this->entityTypeRepository = $entity_type_repository;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('entity_field.manager'),
      $container->get('entity_type.repository'),
      $container->get('request_stack')->getCurrentRequest()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $site_info_config = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $site_info_config->label(),
      '#description' => $this->t("Label for the Site info config."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $site_info_config->id(),
      '#machine_name' => [
        'exists' => '\Drupal\rp_site_info\Entity\SiteInfoConfig::load',
      ],
      '#disabled' => !$site_info_config->isNew(),
    ];

    $form['resource'] = $this->buildCompleteForm($site_info_config);

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $site_info_config = $this->entity;
    $status = $site_info_config->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Site info config.', [
          '%label' => $site_info_config->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Site info config.', [
          '%label' => $site_info_config->label(),
        ]));
    }
    $form_state->setRedirectUrl($site_info_config->toUrl('collection'));
  }

  /**
   * Builds the part of the form that contains the form.
   *
   * @param \Drupal\rp_site_info\Entity\SiteInfoConfig $entity
   *   The config entity backed by this form.
   *
   * @return array
   *   The partial form.
   */
  protected function buildCompleteForm(SiteInfoConfig $entity) {
    $entity_type_id = 'site_info'; $bundle = 'site_info';
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    if ($entity_type instanceof ContentEntityTypeInterface) {
      $field_definitions = array_map(function (FieldDefinitionInterface $field_definition) {
        return ['field_name' => $field_definition->getName(), 'field_type' => $field_definition->getItemDefinition()->getDataType()];
      }, $this->fieldManager->getFieldDefinitions($entity_type_id, $bundle));
    }
    else {
      $field_definitions = array_map(function ($field_key) {
        return ['field_name' => $field_key, 'field_type' => 'field_item:string'];
      }, array_keys($entity_type->getKeys()));
    }

    /** @var \Drupal\rp_site_info\Entity\SiteInfo $site_info */
    $site_info = $this->entityManager->getStorage('site_info')->load($entity->get('site_info'));
    if(is_null($site_info) || empty($site_info))
      $site_info = $this->entityManager->getStorage('site_info')->create();

    $form['config']['entity'] = [
      '#type' => 'details',
      '#title' => $this->t('Entity'),
      '#open' => true
    ];

    $form['config']['entity']['disabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disabled'),
      '#default_value' => $entity->get('disabled'),
    ];

    // $form['config']['entity']['site'] = [
    //   '#type' => 'entity_autocomplete',
    //   '#target_type' => 'site',
    //   '#title' => $this->t('Site'),
    //   '#default_value' => $this->getEntityReferenceValue('site'),
    //   '#tags' => TRUE,
    // ];

    $options = [];
    foreach($this->entityManager->getStorage('site_info')->loadMultiple() as $site_i) {
      $options[$site_i->id()] = $site_i->getName();
    }

    $form['config']['entity']['site_info'] = [
      '#type' => 'select',
      '#target_type' => 'site_info',
      '#title' => $this->t('Site info'),
      '#options' => $options,
      '#default_value' => $entity->get('site_info'),
      '#tags' => TRUE,
    ];

    $form['config']['entity']['site_info_uuid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site info uuid'),
      '#default_value' => $entity->get('site_info_uuid'),
    ];

    $form['config']['entity']['site'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site'),
      '#default_value' => $entity->get('site'),
      '#disabled' => TRUE
    ];

    $form['config']['entity']['region'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Region'),
      '#default_value' => $entity->get('region'),
      '#disabled' => TRUE
    ];

    $form['config']['entity']['language_content'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Language Content'),
      '#default_value' => $entity->get('language_content'),
      '#disabled' => TRUE
    ];
    
    $form['config']['fields'] = [
      '#type' => 'details',
      '#title' => $this->t('Fields'),
      '#open' => TRUE,
    ];

    $form['config']['fields']['resourceFields'] = [
      '#type' => 'table',
      '#header' => [
        'disabled' => $this->t('Disabled'),
        'fieldName' => $this->t('Field name'),
        'fieldValue' => $this->t('Field value'),
      ],
      '#empty' => $this->t('No fields available.'),
      '#states' => [
        'visible' => [
          ':input[name="disabled"]' => ['checked' => FALSE],
        ],
      ],
    ];

    foreach ($field_definitions as $field_values) {
      $form['config']['fields']['resourceFields'][$field_values['field_name']] = $this->buildFormField($field_values['field_name'], $entity, $site_info);
    }
    return $form;
  }

  /**
   * Builds the part of the form for field.
   *
   * @param string $field_name
   *   The field name of the field being overridden.
   * @param \Drupal\rp_site_info\Entity\SiteInfoConfig $entity
   *   The config entity backed by this form.
   *
   * @return array
   *   The partial form.
   */
  protected function buildFormField($field_name, SiteInfoConfig $entity, SiteInfo $site_info) {
    if(!$this->checkFormFieldName($field_name)) return [];
    $resource_fields = array_filter($entity->get('resourceFields'), function (array $resource_field) use ($field_name) {
        return $resource_field['fieldName'] == $field_name;
    });

    $resource_field = array_shift($resource_fields);
    if(empty($resource_field['fieldValue'])) {
      $field_value = $site_info->get($field_name)->value;
    }
    else {
      $field_value = $resource_field['fieldValue'];
    }
    $form = [];
    $form['disabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disabled'),
      '#title_display' => 'hidden',
      '#default_value' => $resource_field['disabled'],
    ];
    $form['fieldName'] = [
      '#type' => 'hidden',
      '#value' => $field_name,
      '#prefix' => $field_name,
    ];
    $form['fieldValue'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field Value'),
      '#title_display' => 'hidden',
      '#default_value' => $field_value,
      '#states' => [
        'visible' => [
          ':input[name="resourceFields[' . $field_name . '][disabled]"]' => [
            'checked' => FALSE,
          ],
        ],
      ],
    ];

    return $form;
  }

  public function checkFormFieldName($field_name) {
    $flag = false;
    // && !in_array($field_name,['field_site_info_site','field_site_info_region','field_site_info_language'])
    if(substr($field_name,0,6) == 'field_') {
      $flag = true;
    }
    return $flag;
  }

  private function getEntityReferenceValue($field_name) {
    $site_info_config = $this->entity;
    $value = $site_info_config->get($field_name);
    if(!is_null($value) && isset($value[0]['target_id'])){
      return $this->entityManager->getStorage($field_name)->load($value[0]['target_id']);
    }
    else
      return '';
  }

}
