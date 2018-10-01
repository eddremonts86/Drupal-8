<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\HourShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering calendars.
 *
 * @Shortcode(
 *   id = "hour",
 *   title = @Translation("hour"),
 *   description = @Translation("Returns event hour.")
 * )
 */
class HourShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        if($node = \Drupal::routeMatch()->getParameter('node')){
            if($node->type->target_id == 'events'){
                $node = $node->toArray();    
                return date("H:i", $node['field_event_date'][0]['value']);
            }
        }

        return date("H:i");
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[hour][/hour]') . '</strong> ';
        $output[] = $this->t('Returns event hour.') . '</p>';

        return implode(' ', $output);
    }

}
