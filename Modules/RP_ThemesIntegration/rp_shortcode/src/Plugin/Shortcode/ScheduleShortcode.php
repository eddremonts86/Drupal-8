<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\ScheduleShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering match advertisement.
 *
 * @Shortcode(
 *   id = "schedule",
 *   title = @Translation("Schedule"),
 *   description = @Translation("Builds an schedule list")
 * )
 */
class ScheduleShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        $attributes = $this->getAttributes(array(
            'type' => '',
            'title' => '',
            'background_image' => ''
        ),
            $attributes
        );

        if ($attributes['type'] == '')
        {
            $theme = 'shortcode_schedule';
        }
        else
        {
            $theme = 'shortcode_schedule' . $attributes['type'];
        }

        $output = array(
            '#theme' => $theme,
            '#title'=> $attributes['title'],
            '#background_image' => $attributes['background_image'],
        );

        return $this->render($output);
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[schedule (type="type" background_image="image.png" title="Page header title")][/schedule]') . '</strong> ';
        $output[] = $this->t('Renders schedule') . '</p>';

        return implode(' ', $output);
    }

}
