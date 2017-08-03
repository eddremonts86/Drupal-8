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
class rebelpenguin_endponts_client_urls extends FormBase
{

    public function buildForm(array $form, FormStateInterface $form_state)
    {


      $controler = new rebelpenguin_endponts_Controller();
      $resp = $controler->getUrls();

        $form['information'] = [
            '#type' => 'vertical_tabs',
            '#default_tab' => 'edit-publication',
        ];
        //----------------------------------------Nodes ----------------------------------
        $form['nodes'] = [
            '#type' => 'details',
            '#title' => 'Nodes',
            '#group' => 'information',
        ];
        $form['nodes']['node'] = [
            '#type' => 'textfield',
            '#title' => t('One Node'),
            '#placeholder' => $resp['node']
        ];
        $form['nodes']['onerevisions'] = [
            '#type' => 'textfield',
            '#title' => t('All revisions by node'),
            '#placeholder' => $resp['onerevisions']
        ];
        $form['nodes']['allnodes'] = [
            '#type' => 'textfield',
            '#title' => t('All Nodes'),
            '#placeholder' => $resp['allnodes']
        ];
        $form['nodes']['revisions'] = [
            '#type' => 'textfield',
            '#title' => t('Alll Revisions '),
            '#placeholder' => $resp['revisions']
        ];
        //----------------------------------------Alias ----------------------------------
        $form['alias'] = [
            '#type' => 'details',
            '#title' => t('Alias'),
            '#group' => 'information',
        ];
        $form['alias']['allalias'] = [
            '#type' => 'textfield',
            '#title' => t('All Alias'),
            '#placeholder' => $resp['allalias']
        ];
        $form['alias']['aliasbyid'] = [
            '#type' => 'textfield',
            '#title' => t('Alias by id'),
            '#placeholder' => $resp['aliasbyid']
        ];
        //----------------------------------------Taxonomy ----------------------------------
        $form['Taxonomy'] = [
            '#type' => 'details',
            '#title' => t('Taxonomys and C. type'),
            '#group' => 'information',
        ];
        $form['Taxonomy']['alltaxonomy'] = [
            '#type' => 'textfield',
            '#title' => t('All Taxonomy'),
            '#placeholder' => $resp['alltaxonomy']
        ];
        $form['Taxonomy']['allctypes'] = [
            '#type' => 'textfield',
            '#title' => t('All Content Types'),
            '#placeholder' => $resp['allctypes']
        ];
        //----------------------------------------Users ----------------------------------
        $form['user'] = [
            '#type' => 'details',
            '#title' => t('User'),
            '#group' => 'information',
        ];
        $form['user']['alluser'] = [
            '#type' => 'textfield',
            '#title' => t('All User'),
            '#placeholder' => $resp['alluser']
        ];
        //----------------------------------------Submit ----------------------------------
        $form['actions'] = ['#type' => 'actions'];
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save'),
        ];
        return $form;
    }

    public function getFormId()
    {
        return 'rebelpenguin_endponts_config_urls';
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $values = $form_state->getValues();
        $controler = new rebelpenguin_endponts_Controller();
        $controler->saveUrls($values);

    }
}
