<?php

namespace Drupal\rp_api\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Configure RP API settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['rp_api.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['example'] = array(
      '#type' => 'textfield',
      '#title' => t('Example'),
      '#default_value' => $this->config('rp_api.settings')->get('example'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * @DCG: Optional.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('example') != 'example') {
      $form_state->setErrorByName('example', $this->t('The value is not correct.'));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('rp_api.settings')
      ->set('example', $form_state->getValue('example'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
