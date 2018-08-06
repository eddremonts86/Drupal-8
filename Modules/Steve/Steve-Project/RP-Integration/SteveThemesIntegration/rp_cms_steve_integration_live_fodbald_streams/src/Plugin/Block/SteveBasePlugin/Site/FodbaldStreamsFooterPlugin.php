<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Site;

use Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller\LiveFodbaldStreamsController;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'fodbaldstreamsfooterplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsfooterplugin",
 *  admin_label = @Translation("Fodbald Streams Footer Plugin"),
 * )
 */
class FodbaldStreamsFooterPlugin extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new LiveFodbaldStreamsController();
	
	$config = $this->getConfiguration();
    
    $data =  [
      '#theme' => 'fodbaldstreamsfooterplugin',
      '#tags' => $class->buildFooterMenu('football_streams_footer_menu', 'fodbald_str'),
      '#rights' => isset($config['rights']) && !empty($config['rights']) ? $config['rights'] : t('fodbald_streams_footer_rights'),
      '#developedby' => isset($config['developedby']) && !empty($config['developedby']) ? $config['developedby'] : t('fodbald_streams_footer_developed_by'),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['rights'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Rights text:'),
      '#default_value' => isset($config['rights']) ? $config['rights'] : '',
    ];
    
    $form['developedby'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Developed by text:'),
      '#default_value' => isset($config['developedby']) ? $config['developedby'] : '',
    ];

    return $form;
  }
  
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['developedby'] = $values['developedby'];
	$this->configuration['rights'] = $values['rights'];
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

