<?php
namespace Drupal\rp_ad_block\Plugin\Block\AdBlock;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'adblockplugin' block.
 *
 * @Block(
 *  id = "adblockplugin",
 *  admin_label = @Translation("Ad Block Plugin"),
 * )
 */
class AdBlockPlugin extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$config = $this->getConfiguration();
	
    $data =  [
      '#theme' => 'adblockplugin',
      '#data' => isset($config['text']) ? $config['text'] : NULL,
    ];
    return $data;
  }

   public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    
    $form['text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Ad Block Warning.'),
      '#default_value' => isset($config['text']) ? $config['text'] : '',
    ];

    return $form;
  }
  
   public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['text'] = $values['text'];
  }

  public function getCacheTags() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
    }
    else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }
}

