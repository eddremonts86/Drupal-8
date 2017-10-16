<?php

namespace Drupal\rp_base\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RPBaseConfig.
 *
 * @package Drupal\rp_base\Form
 */
class RPBaseConfig extends ConfigFormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rp_base_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
        'rp_base.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('rp_base.settings');
    $form['rp_base_site_api_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site API ID'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' =>  $config->get('rp_base_site_api_id'),
      '#description' => $this->t('Use in drush command like this drush -y config-set rp_base.settings rp_base_site_api_id SITE_ID_NUMBER'),
      '#required' => TRUE
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('rp_base.settings')
      // Set the submitted configuration setting
      ->set('rp_base_site_api_id', $form_state->getValue('rp_base_site_api_id'))
      ->save();

    parent::submitForm($form, $form_state);

  }

}
