<?php

namespace Drupal\rp_cms_site_info\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CmsSiteInfoForm.
 *
 * @package Drupal\rp_cms_site_info\Form
 */
class CmsSiteInfoForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $cms_site_info = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $cms_site_info->label(),
      '#description' => $this->t("Label for the CMS Site Info."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $cms_site_info->id(),
      '#machine_name' => [
        'exists' => '\Drupal\rp_cms_site_info\Entity\CmsSiteInfo::load',
      ],
      '#disabled' => !$cms_site_info->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $cms_site_info = $this->entity;
    $status = $cms_site_info->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label CMS Site Info.', [
          '%label' => $cms_site_info->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label CMS Site Info.', [
          '%label' => $cms_site_info->label(),
        ]));
    }
    $form_state->setRedirectUrl($cms_site_info->toUrl('collection'));
  }

}
