<?php

namespace Drupal\rp_sites\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the site entity edit forms.
 */
class SiteForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_aruments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      drupal_set_message($this->t('Created new site %label.', $message_arguments));
      $this->logger('rp_sites')->notice('Creaqted new site %label', $logger_aruments);
    }
    else {
      drupal_set_message($this->t('Updated site %label.', $message_arguments));
      $this->logger('rp_sites')->notice('Creaqted new site %label.', $logger_aruments);
    }

    $form_state->setRedirect('entity.rp_site.canonical', ['rp_site' => $entity->id()]);
  }

}
