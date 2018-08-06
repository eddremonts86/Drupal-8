<?php

namespace Drupal\rp_admin_events\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rp_admin_events\Controller\Eventsadmin;
use Drupal\Core\Url;

/**
 * Class eventList.
 */
class eventList extends FormBase
{


    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'eventlist';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $backObj = new  Eventsadmin();
        $id = \Drupal::routeMatch()->getParameter('id');
        $Nodelist = $backObj->getEvent($id);
        $data = $backObj->renderTableEvent($Nodelist);
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Create New Event'),
        ];

        $form['Events'] = array(
            '#type' => 'fieldset',
            '#title' => $this
                ->t('Event Referent List'),
        );
        $form['Events']['table'] = [
            '#type' => 'tableselect',
            '#header' => [
                'EventName' => t('Event name'),
                'Sport' => t('Site'),
                'eventDate' => t('Region'),
                'Leage' => t('LEAGUE'),
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
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $id = \Drupal::routeMatch()->getParameter('id');
        $formData = $form_state->getValues();
        $form_state->setRedirect(
            'entity.game_info.add_form', [
                'edit[field_game_info_game]' => $id,
                'edit[name]' => \Drupal::routeMatch()->getParameter('name'),
                'destination' => '/admin/rp/entity-content/game'
            ]
        );

    }

}
