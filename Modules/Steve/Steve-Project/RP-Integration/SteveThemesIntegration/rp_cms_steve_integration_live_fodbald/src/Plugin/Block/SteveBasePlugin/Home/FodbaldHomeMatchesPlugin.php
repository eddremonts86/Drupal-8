<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Home;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rp_block_data_configuration\Controller\BlocksDataConfigs;

/**
 * Provides a 'fodbaldhomematchesplugin' block.
 *
 * @Block(
 *  id = "fodbaldhomematchesplugin",
 *  admin_label = @Translation("Fodbald Home Matches Plugin"),
 * )
 */
class FodbaldHomeMatchesPlugin extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()    {

        $controllerObject = new LiveFodbaldController();

        $data = [
            '#theme' => 'fodbaldhomematchesplugin',
            '#tags' => $controllerObject->getSchedulePlusTree(0, "Y-m-d", 1, 0, $controllerObject->getSport(2, 'api'), NULL, NULL, ['LiveFodbaldScheduleFormatModificator']),
            '#article' => $controllerObject->getFodbaldPreviews(10, null, 2),
            '#background' => [],
            '#colors' => []
        ];
        return $data;
    }

    public function getCacheTags()    {
        if ($node = \Drupal::routeMatch()->getParameter('node')) {
            return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
        } else {
            //Return default tags instead.
            return parent::getCacheTags();
        }
    }

    public function getCacheContexts()    {
        return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
    }

    /*Configuration */

    public function blockForm($form, FormStateInterface $form_state){
        $form = parent::blockForm($form, $form_state);
        $form['configExternal'] = [
            '#type' => 'checkbox',
            '#title' => t('Use external block config (To see the configuration page <a href="/admin/block-configuration">clik here</a>)'),
            '#placeholder' => t('All revisions by node - test'),
            '#default_value' => BlocksDataConfigs::getBlock('fodbaldhomematchesplugin')
        ];
        return $form;
    }
    public function blockSubmit($form, FormStateInterface $form_state){
        BlocksDataConfigs::createConfigBlock($form_state,'fodbaldhomematchesplugin');
    }

    
}



