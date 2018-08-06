<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Events;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
use Drupal\rp_block_data_configuration\Controller\BlocksDataConfigs;

/**
 * Provides a 'EventPageFooterPlugin' block.
 *
 * @Block(
 *  id = "eventpagefooterplugin",
 *  admin_label = @Translation("Event Page Footer"),
 * )
 */
class EventPageFooterPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'eventpagefooterplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => [],
    ];
  }
  public function getCacheTags() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }
   /*---------------------------- Configuration ------------------ */

   public function blockForm($form, FormStateInterface $form_state){
    $form = parent::blockForm($form, $form_state);
    $form['configExternal'] = [
        '#type' => 'checkbox',
        '#title' => t('Use external block config (To see the configuration page <a href="/admin/block-configuration">clik here</a>)'),
        '#placeholder' => t('All revisions by node - test'),
        '#default_value' => BlocksDataConfigs::getBlock('eventpagefooterplugin')
    ];
    return $form;
  }
  public function blockSubmit($form, FormStateInterface $form_state){
      BlocksDataConfigs::createConfigBlock($form_state,'eventpagefooterplugin');
  }
  /*-------------------------------------------------------------- */

  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }

}
