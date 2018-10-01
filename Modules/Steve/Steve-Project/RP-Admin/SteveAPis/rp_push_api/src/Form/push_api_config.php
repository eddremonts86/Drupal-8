<?php

namespace Drupal\rp_push_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rp_push_api\Controller\pushController;
use Cocur\Slugify\Slugify;

/**
 * Class push_api_config.
 */
class push_api_config extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'push_api_config';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $slugify = new  Slugify();
    $site = new  pushController();
    $list =$site->getlocalcontenttype();
    $form['push_information'] = array(
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-site',
    );

    if($site->getEntityConf('server') == 1) {
      $uuid_service = \Drupal::service('uuid');
      $form['sites'] = array(
        '#type' => 'details',
        '#title' => $this->t('Site List.'),
        '#group' => 'push_information',
      );
      $form['sites']['form'] = [
        '#type' => 'fieldset',
        '#title' => $this
          ->t('Add New Site'),
      ];
      $form['sites']['form']['SiteName'] = [
        '#type' => 'textfield',
        '#title' => 'Site Name',
        '#maxlength' => 64,
        '#size' => 64,
        '#default_value' => '',
        '#description' => '',
      ];
      $form['sites']['form']['ClientToken'] = [
        '#type' => 'textfield',
        '#title' => 'Client Token',
        '#maxlength' => 64,
        '#size' => 64,
        '#default_value' => $uuid_service->generate(),
        '#description' => '',
      ];
      $form['sites']['form']['ServerToken'] = [
        '#type' => 'textfield',
        '#title' => 'Server Token',
        '#maxlength' => 64,
        '#size' => 64,
        '#default_value' => $uuid_service->generate(),
        '#description' => '',
      ];
      $form['sites']['table'] = [
        '#type' => 'tableselect',
        '#header' => [
          'siteName' => t('Sites Name'),
          'cToken' => t('Clinet Token'),
          'sToken' => t('Server Token'),
          //      'active' => t('Active'),
          'OPERATIONS' => t('Edit Event'),
        ],
        '#options' => $site->renderSiteTable(),
        '#open' => TRUE,
        '#empty' => t('No sites found'),
        '#attributes' => ['id' => 'tableid']
      ];
    }

    $form['server'] = array(
      '#type' => 'details',
      '#title' => $this->t('Server Configuration'),
      '#group' => 'push_information',
    );
    $form['server']['server'] = [
      '#type' => 'checkbox',
      '#title' =>'Export from Server (This Site) to clients',
      '#default_value' => $site->getEntityConf('server'),
    ];
    if($site->getEntityConf('server') == 1) {
      $form['server']['tcontent'] = [
        '#type' => 'fieldset',
        '#title' => $this
          ->t('Content Types'),
      ];
      $form['server']['taxonomies'] = [
        '#type' => 'fieldset',
        '#title' => $this
          ->t('Taxonomies'),
      ];
    }

    $form['client'] = array(
      '#type' => 'details',
      '#title' => $this->t('Client Configuration'),
      '#group' => 'push_information',
    );
    $form['client']['client'] = [
      '#type' => 'checkbox',
      '#title' =>'Import from server to client (This Site)',
      '#default_value' => $site->getEntityConf('client'),
    ];
    if($site->getEntityConf('client') == 1) {
      $form['client']['tcontent'] = [
        '#type' => 'fieldset',
        '#title' => $this
          ->t('Import Content Types')
      ];
      $form['client']['taxonomies'] = [
        '#type' => 'fieldset',
        '#title' => $this
          ->t('Import Taxonomies'),
      ];
    }

    foreach ($list["node"] as $elemente) {
      if($site->getEntityConf('client') == 1) {
        $form['client']['tcontent'][$slugify->slugify('client-node-'.$elemente)] = [
        '#type' => 'checkbox',
        '#title' => $this->t($elemente),
        '#default_value' => $site->getEntityConf('client-node-'.$elemente),
      ];
      }
      if($site->getEntityConf('server') == 1) {
        $form['server']['tcontent'][$slugify->slugify('server-node-'.$elemente)] = [
        '#type' => 'checkbox',
        '#title' => $this->t($elemente),
        '#default_value' => $site->getEntityConf('server-node-'.$elemente),
      ];
      }
    }
    foreach ($list["taxonomy"] as $elemente) {
      if($site->getEntityConf('client') == 1) {
        $form['client']['taxonomies'][$slugify->slugify('client-taxonomies-'.$elemente)] = [
          '#type' => 'checkbox',
          '#title' => $this->t($elemente),
          '#default_value' => $site->getEntityConf('client-taxonomies-' . $elemente),
        ];
      }
      if($site->getEntityConf('server') == 1) {
        $form['server']['taxonomies'][$slugify->slugify('server-taxonomies-'.$elemente)] = [
        '#type' => 'checkbox',
        '#title' => $this->t($elemente),
        '#default_value' => $site->getEntityConf('server-taxonomies-'.$elemente),
      ];
      }
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save  configuration'),
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
    $site = new  pushController();
    $dontuse =array(
      'submit',
      'form_id',
      'form_token',
      'form_build_id',
      'push_information__active_tab',
      'table',
      'op',
      'SiteName',
      'ClientToken',
      'ServerToken',
      'sitebutom',
    );
    echo "<pre>";
    foreach ($form_state->getValues() as $key => $value) {
      if(!in_array($key,$dontuse)){
        $site->insertConfg($key , $value);
      }
    }
   $ClientToken =  $form_state->getValue('ClientToken');
   $SiteName =  $form_state->getValue('SiteName');
   $ServerToken = $form_state->getValue('ServerToken');
   if($SiteName and  $ClientToken and  $ServerToken){
     $site->insertSite($SiteName , $ClientToken, $ServerToken);
   }
    //exit();
  }



}
