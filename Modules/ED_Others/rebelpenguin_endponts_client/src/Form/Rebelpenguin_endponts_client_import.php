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
class rebelpenguin_endponts_client_import extends FormBase {

  public function buildForm(array $form, FormStateInterface $form_state) {


    $controler = new rebelpenguin_endponts_Controller();
    $resp = $controler->getUrls();

    $form['information'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-publication',
    ];
    //----------------------------------------Nodes ----------------------------------
    $form['nodes'] = [
      '#type' => 'details',
      '#title' => 'Get Node by id',
      '#group' => 'information',
    ];
    $form['nodes']['onerevisions'] = [
      '#type' => 'textfield',
      '#title' => t('Nodo id'),
      '#placeholder' => '2541',
      '#required' => TRUE,
    ];
    $form['nodes']['radios'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => t('Do you want import any revision?'),
      '#options' => [
        'none' => $this->t('Dont import revision'),
       // 'only' => $this->t('Only revisions'),
        'one' => $this->t('Only the last revision'),
        'all' => $this->t('All revisions'),
      ],
    ];
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
    $nodeid = $values['onerevisions'];
    $revision = $values['radios'];

    //--------------------------only the node------------------
    if($revision == 'none'){
      $controler = new rebelpenguin_endponts_Controller();
      $preurle = $controler->lookforurls('node');
      $url = $preurle . $nodeid;
      $arra = $controler->api_conect($url);
      $controler->nodes_import($arra);
      $message = $this->t($revision);
      drupal_set_message($message);
    }
    //--------------------------only the node------------------
   if($revision == 'one' || $revision == 'all' ){
      $controler = new rebelpenguin_endponts_Controller();
      $preurle = $controler->lookforurls('node');
      $url = $preurle . $nodeid;
      $arra = $controler->api_conect($url);
      $controler->nodes_import($arra);
      $controler->getrevisions($nodeid,$revision);
      $message = $this->t($revision);
      drupal_set_message($message);
    }
    //----------------------
    if($revision == 'only' ){
           @$node_ = Node::load($nodeid);

      if(isset($node_)){
        $controler = new rebelpenguin_endponts_Controller();
        $controler->getrevisions($nodeid,$revision);
        $message = $this->t($revision);
        drupal_set_message($message);
      }
     else{
       $message = $this->t('Node not exist on this system. Please import this node firts.');
       drupal_set_message($message);
     }


    }
  }
}
