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
      ];

    $form['rp_base_site_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site url'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' =>  $config->get('rp_base_site_url'),
      '#description' => $this->t(''),
      ];


    $form['rp_base_site_url_api'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site url API '),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' =>  $config->get('rp_base_site_url_api'),
      '#description' => $this->t(''),
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
    $data = $this->config('rp_base.settings');

    $data->set('rp_base_site_api_id', $form_state->getValue('rp_base_site_api_id'))->save();
    $data->set('rp_base_site_url', $form_state->getValue('rp_base_site_url'))->save();
    $data->set('rp_base_site_url_api', $form_state->getValue('rp_base_site_url_api'))->save();

    parent::submitForm($form, $form_state);

  }

}
