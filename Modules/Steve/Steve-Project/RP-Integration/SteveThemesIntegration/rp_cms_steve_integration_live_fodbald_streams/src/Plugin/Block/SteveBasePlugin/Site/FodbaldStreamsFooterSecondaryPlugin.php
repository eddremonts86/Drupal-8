<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Site;

use Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller\LiveFodbaldStreamsController;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'fodbaldstreamsfootersecondaryplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsfootersecondaryplugin",
 *  admin_label = @Translation("Fodbald Streams Footer Secondary Plugin"),
 * )
 */

class FodbaldStreamsFooterSecondaryPlugin extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new LiveFodbaldStreamsController;
	
	$config = $this->getConfiguration();
	
    $data =  [
      '#theme' => 'fodbaldstreamsfootersecondaryplugin',
      '#tags' => $class->getSocialNetworks(),
      '#background' => [],
      '#address' => isset($config['address']) && !empty($config['address']) ? $config['address'] : t('fodbald_streams_footer_addr_text'),
      '#email' => isset($config['email']) && !empty($config['email']) ? $config['email'] : \Drupal::config('system.site')->get('mail'),
      '#colors' => [],
      '#front' => \Drupal::service('path.matcher')->isFrontPage()
    ];
    return $data;
  }
  
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address:'),
      '#default_value' => isset($config['address']) ? $config['address'] : '',
    ];
    
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#default_value' => isset($config['email']) ? $config['email'] : '',
    ];

    return $form;
  }
  
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['address'] = $values['address'];
	$this->configuration['email'] = $values['email'];
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

