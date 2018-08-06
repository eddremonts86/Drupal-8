<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\DayShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering calendars.
 *
 * @Shortcode(
 *   id = "day",
 *   title = @Translation("Day"),
 *   description = @Translation("Returns event day.")
 * )
 */
class DayShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        if($node = \Drupal::routeMatch()->getParameter('node')){
            if($node->type->target_id == 'events'){
                $node = $node->toArray();    
                return date("d-m-Y", $node['field_event_date'][0]['value']);
            }
        }

        return date("d-m-Y");
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[day][/day]') . '</strong> ';
        $output[] = $this->t('Returns event day.') . '</p>';

        return implode(' ', $output);
    }

}
