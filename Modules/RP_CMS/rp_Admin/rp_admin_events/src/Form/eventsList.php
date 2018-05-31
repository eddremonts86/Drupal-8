<?php

namespace Drupal\rp_admin_events\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rp_admin_events\Controller\Eventsadmin;
/**
 * Class eventsList.
 */
class eventsList extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'eventsList';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

      $backObj = new  Eventsadmin();
      $date = date("d-m-Y");
      $Nodelist = $backObj->getAllEvents(1);
      $data = $backObj->renderTable($Nodelist);

      $form['Events'] = array(
          '#type' => 'fieldset',
          '#title' => $this
              ->t('Events List'),
      );
      $form['Events']['table'] = [
          '#type' => 'tableselect',
          '#header' => [
              'EventName' => t('Event name'),
              'countRelated' => t('Related Articles'),
              'Sport' => t('SPORT'),
              'Leage' => t('LEAGUE'),
              'eventDate' => t('DATE'),
              'OPERATIONS' => t('Edit Event'),
          ],
          '#options' => $data,
          '#open' => TRUE,
          '#empty' => t('No users found'),
          '#attributes' => ['id' => 'tableid']
      ];
      $form['Events']['pager'] = array(
          '#type' => 'pager'
      );
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
