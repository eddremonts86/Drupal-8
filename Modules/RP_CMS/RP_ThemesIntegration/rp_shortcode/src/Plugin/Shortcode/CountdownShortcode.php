<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\CountdownShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering calendars.
 *
 * @Shortcode(
 *   id = "countdown",
 *   title = @Translation("Countdown"),
 *   description = @Translation("Returns event countdown.")
 * )
 */
class CountdownShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        if($node = \Drupal::routeMatch()->getParameter('node')){
            if($node->type->target_id == 'events'){
                $node = $node->toArray();
        
                return '<span class="shortcodeCountdown" startdate="'.date( "Y/m/d H:i:s", $node['field_event_date'][0]['value']).'">'.date("Y/m/d H:i:s", $node['field_event_date'][0]['value']).'</span>';
            }
        }

        return 'countdown';
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[countdown][/countdown]') . '</strong> ';
        $output[] = $this->t('Returns event countdown.') . '</p>';

        return implode(' ', $output);
    }

}
