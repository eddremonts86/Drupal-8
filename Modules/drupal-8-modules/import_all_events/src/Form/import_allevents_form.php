<?php

namespace Drupal\import_all_events\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\import_all_events\Controller\import_all_events_controller;

/**
 * Implements the vertical tabs demo form controller.
 *
 * This example demonstrates the use of \Drupal\Core\Render\Element\VerticalTabs
 * to group input elements according category.
 *
 * @see \Drupal\Core\Form\FormBase
 * @see \Drupal\Core\Form\ConfigFormBase
 */
class import_allevents_form extends FormBase {

  public function buildForm(array $form, FormStateInterface $form_state) {
    $controler = new  import_all_events_controller();
    $types = $controler->getlocalcontenttype();
    //----------------------------------------Nodes ----------------------------------
    $form['nodes']['onerevisions'] = [
      '#type' => 'textfield',
      '#title' => t('API URL'),
      '#placeholder' => 'http://api.com/getevents',
      '#required' => TRUE,
    ];
    $form['nodes']['radios'] = [
      '#type' => 'radios',
      '#title' => t('Content types to import'),
      '#options' =>$types,
    ];
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];
    return $form;
  }
  public function getFormId() {
    return '_endponts_config_import';
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
   $cont =  new import_all_events_controller();
   $url='http://163.172.20.91:9355/api/v2.0/schedule/2017-04-30';
   $cont->getrevisions($url);
  }
}
