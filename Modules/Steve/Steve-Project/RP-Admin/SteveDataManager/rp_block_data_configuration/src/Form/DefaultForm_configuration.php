<?php

namespace Drupal\rp_block_data_configuration\Form;

use Composer\Installers\WHMCSInstaller;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rp_block_data_configuration\Controller\BlocksDataConfigs;

/**
 * Class DefaultForm_configuration.
 */
class DefaultForm_configuration extends FormBase
{


    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'DefaultForm_configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
      $blockConf = BlocksDataConfigs::getBlockToConfig();
      if (isset($blockConf )) {

          $form['information'] = [
            '#type' => 'vertical_tabs',
            '#default_tab' => 'edit-publication',
          ];
             foreach ($blockConf as $block) {
              $form['nodes' . $block['blockname']] = [
                    '#type' => 'details',
                    '#title' => $block['blockname'],
                    '#group' => 'information',
                ];
                $form['nodes' . $block['blockname']]['tournaments_weight'] = [
                    '#type' => 'textfield',
                    '#title' => t('Leagues under # weight'),
                    '#placeholder' => $block['tournaments_weight'],
                    '#default_value' =>$block['tournaments_weight'],
                ];
               $form['nodes' . $block['blockname']]['event_number'] = [
                 '#type' => 'textfield',
                 '#title' => t('Number of events'),
                 '#placeholder' => $block['event_number'],
                 '#default_value' =>$block['event_number'],
               ];
               $form['nodes' . $block['blockname']]['only_promotion']= [
                 '#type' => 'checkbox',
                 '#title' => t('Only "Promote Events" '),
                 '#placeholder' => t('All revisions by node - test'),
                 '#default_value' => $block['only_promotion']
               ];
               $form['nodes' . $block['blockname']]['blockid'] = [
                 '#type' => 'textfield',
                 '#title' => t('Block id'),
                 '#placeholder' => $block['blockid'],
                 '#default_value' =>$block['blockid']
               ];
                $form['nodes' . $block['blockname']]['submit'] = [
                    '#type' => 'submit',
                    '#value' => $this->t('Save "'.$block['blockname'].'" configuration'),
                ];
            }
        }else {
            drupal_set_message('no hay nada.');
        }
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)    {
        $data=array();
        foreach ($form_state->getValues() as $key => $value) {
          $data[$key] = $value;
        }
        $blockConf = BlocksDataConfigs::saveBlockConfiguration($data);
        return true;
    }

}
