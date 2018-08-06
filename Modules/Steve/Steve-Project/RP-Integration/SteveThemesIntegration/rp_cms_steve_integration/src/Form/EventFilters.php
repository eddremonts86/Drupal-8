<?php

namespace Drupal\rp_cms_steve_integration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rp_cms_steve_integration\Controller\SteveBackendControler;

/**
 * Class EventFilters.
 */
class EventFilters extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_filters';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $backObj = new  SteveBackendControler();
    $sportList = $backObj->getSportPages();
    $date = date("d-m-Y");
    if (@$_GET['sport'] and @$_GET['date']) {
      $sport = $_GET['sport'];
      $date = $_GET['date'];
      $Nodelist = $backObj->getSchedulebydate($sport, $date);
      $data = $backObj->renderTable($Nodelist);

    }
    else {
      foreach ($sportList as $key => $value) {
        $Nodelist = $backObj->getAllScheduleBySport($key);
        $data = $backObj->renderTable($Nodelist);
        break;
      }
    }


    $form['sport'] = [
      '#type' => 'select',
      '#title' => $this->t('Sport'),
      '#options' => [$sportList],
      '#size' => 1,
    ];
    $form['event_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Event date'),
      '#date_format' => 'd-m-Y',
      '#default_value' => $date,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];
    $form['table'] = [
      '#type' => 'tableselect',
      '#header' => [
        'EventTitle' => t('TITLE'),
        'Sport' => t('SPORT'),
        'Leage' => t('LEAGUE'),
        'eventDate' => t('DATE'),
        'OPERATIONS' => t('OPERATIONS'),
      ],
      '#options' => $data,
      '#open' => TRUE,
      '#empty' => t('No users found'),
      '#attributes' => ['id' => 'tableid'],
    ];
    $form['pager'] = [
      '#type' => 'pager',
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
    $formData = $form_state->getValues();
    drupal_set_message('Events to ' . $formData["event_date"]);
    $data = [
             'sport' => $formData["sport"],
             'date' => $formData["event_date"]
            ];
    $form_state->setRedirect('rp_cms_steve_integration.event_filters', $data);
  }

}
