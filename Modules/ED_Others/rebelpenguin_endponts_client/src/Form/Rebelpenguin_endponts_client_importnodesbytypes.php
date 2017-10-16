<?php

namespace Drupal\rebelpenguin_endponts_client\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rebelpenguin_endponts_client\Controller\Rebelpenguin_endponts_Controller;

/**
 * Implements the vertical tabs demo form controller.
 *
 * This example demonstrates the use of \Drupal\Core\Render\Element\VerticalTabs
 * to group input elements according category.
 *
 * @see \Drupal\Core\Form\FormBase
 * @see \Drupal\Core\Form\ConfigFormBase
 */
class rebelpenguin_endponts_client_importnodesbytypes extends FormBase {

  public function buildForm(array $form, FormStateInterface $form_state) {
    $controler = new rebelpenguin_endponts_Controller();
    $types = $controler->getlocalcontenttype();

    $form['information'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-publication',
    ];
    //----------------------------------------Nodes ----------------------------------
    $form['nodes'] = [
      '#type' => 'details',
      '#title' => 'Get all Node by type',
      '#group' => 'information',
    ];
    $form['nodes']['radios'] = [
      '#type' => 'radios',
      '#title' => t('Content types to import'),
      '#options' =>$types,
    ];
    /*$form['newnodes'] = [
      '#type' => 'details',
      '#title' => 'Get all new nodes ',
      '#group' => 'information',
    ];
    $form['newnodes']['newradios'] = [
      '#type' => 'radios',
      '#title' => t('Import new nodes by CType'),
      '#options' =>$types,
    ];*/
    //-------------------------------------
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];
    return $form;
  }

  public function getFormId() {
    return 'rebelpenguin_endponts_config_import';
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $revision = $values['radios'];
    $newrevision = $values['newradios'];
    $controler = new rebelpenguin_endponts_Controller();
    if($revision  !=''){
      $data = $controler->importnodesbytype($revision);
      if($data){
        $message = $this->t('').$data;
        drupal_set_message($message);
      }

    }
    else{
      $data = $controler->importnodesbytype($newrevision);
      if($data){
        $message = $this->t('Node not exist on this system. Please import this node firts.');
        drupal_set_message($message);
      }
      }


  }
}
