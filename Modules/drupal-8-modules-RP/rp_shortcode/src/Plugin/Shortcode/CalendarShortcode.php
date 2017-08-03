<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\CalendarShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering calendars.
 *
 * @Shortcode(
 *   id = "calendar",
 *   title = @Translation("Calendar"),
 *   description = @Translation("Builds an calendar")
 * )
 */
class CalendarShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        $attributes = $this->getAttributes(array(
            'type' => '',
            'title' => '',
            'subtitle' => '',
            'days_count' => '',
        ),
            $attributes
        );

        if ($attributes['type'] == '')
        {
            $theme = 'shortcode_calendar';
        }
        else
        {
            $theme = 'shortcode_calendar_' . $attributes['type'];
        }

        $output = array(
            '#theme' => $theme,
            '#title'=> $attributes['title'],
            '#subtitle'=> $attributes['title'],
        );

        return $this->render($output);
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[calendar (type="type" title="A title" subtitle="A subtitle")][/calendar]') . '</strong> ';
        $output[] = $this->t('Renders calendars') . '</p>';

        return implode(' ', $output);
    }

}
