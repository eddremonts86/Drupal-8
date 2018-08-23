<?php

namespace Drupal\rp_site_generator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rp_site_generator\Controller\siteGeneratorController;

/**
 * Class siteGenerator.
 */
class siteGenerator extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'site_generator';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $sites =   new siteGeneratorController();
    $form['Events'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Site Generator - Note: we always use the last configuration.'),
    );
    $form['Events']['table'] = [
      '#type' => 'tableselect',
      '#header' => [
        'sites' => t('Sites Name'),
        'type' => t('Type ID'),
        'url' => t('Site URL'),
        'configNumber' => t('# config'),
        'OPERATIONS' => t('Edit Event'),
      ],
      '#options' => $sites->renderTable(),
      '#open' => TRUE,
      '#empty' => t('No users found'),
      '#attributes' => ['id' => 'tableid']
    ];
    return $form;
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
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
