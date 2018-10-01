<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\DateShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering calendars.
 *
 * @Shortcode(
 *   id = "date",
 *   title = @Translation("Date"),
 *   description = @Translation("Returns event date.")
 * )
 */
class DateShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        if($node = \Drupal::routeMatch()->getParameter('node')){
            if($node->type->target_id == 'events'){
                $node = $node->toArray();    
                return date("d-m-Y : H:i", $node['field_event_date'][0]['value']);
            }
        }

        return date("d-m-Y : H:i");
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[date][/date]') . '</strong> ';
        $output[] = $this->t('Returns event date.') . '</p>';

        return implode(' ', $output);
    }

}
