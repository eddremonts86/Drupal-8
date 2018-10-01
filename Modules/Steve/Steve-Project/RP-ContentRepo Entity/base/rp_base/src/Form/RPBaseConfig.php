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
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('rp_base.settings');
    $server = $config->get('rp_base_site_server');
    $url = $config->get('rp_base_site_api');

    $form['information'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-site',
    ];
    $form['api'] = [
      '#type' => 'details',
      '#title' => $this->t('Site API configurations'),
      '#group' => 'information',
    ];
    $form['api']['rp_base_site_api'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Steve Server API'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => (isset($url)) ? $url : 'http://steveapi.rebelpenguin.dk:8000',
      '#description' => $this->t(''),
    ];
    $form['api']['rp_base_site_api_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site API ID'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('rp_base_site_api_id'),
      '#description' => $this->t(''),
    ];
    $form['api']['rp_base_site_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site url'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('rp_base_site_url'),
      '#description' => $this->t(''),
    ];
    $form['api']['rp_base_site_url_api'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site url API '),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('rp_base_site_url_api'),
      '#description' => $this->t(''),
    ];
    $form['api']['rp_base_site_server'] = [
      '#type' => 'radios',
      '#title' => $this->t('This site will provide a "CONTENT API" to others sites? '),
      '#default_value' => isset($server) ? $server : 0,
      '#options' => [
        1 => $this->t('YES'),
        0 => $this->t('NO'),
      ],
    ];
    $form['site'] = [
      '#type' => 'details',
      '#title' => $this
        ->t('Site configuration'),
      '#group' => 'information',
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
    $config_factory = \Drupal::configFactory();
    $config = $config_factory->getEditable('rp_base.settings');
    $formValues = $form_state->cleanValues()->getValues('');
    foreach ($formValues as $values => $key) {
      $config->set($values, $form_state->getValue($values))->save();
    }
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'rp_base.settings',
    ];
  }
}
