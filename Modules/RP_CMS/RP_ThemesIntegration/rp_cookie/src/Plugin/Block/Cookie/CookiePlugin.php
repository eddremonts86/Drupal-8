<?php

namespace Drupal\rp_cookie\Plugin\Block\Cookie;

use Drupal\rp_cookie\Controller\CookieController;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'cookieplugin' block.
 *
 * @Block(
 *  id = "cookieplugin",
 *  admin_label = @Translation("Cookie Plugin"),
 * )
 */
class CookiePlugin extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new CookieController();

	$config = $this->getConfiguration();
	
	$duration = isset($config['duration']) ? $config['duration'] : 86400;
	$link = isset($config['link']) ? $config['link'] : '';
	
    $data =  [
      '#theme' => 'cookieplugin',
      '#data' => $class->getCookieBlock($duration, $link)
    ];
    return $data;
  }

   public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link to cookie policy page.'),
      '#default_value' => isset($config['link']) ? $config['link'] : '',
    ];
    
    $form['duration'] = [
      '#type' => 'number',
      '#title' => $this->t('Cookie Duration.'),
      '#default_value' => isset($config['duration']) ? $config['duration'] : 86400,
    ];

    return $form;
  }
  
   public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['link'] = $values['link'];
    $this->configuration['duration'] = $values['duration'];
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

