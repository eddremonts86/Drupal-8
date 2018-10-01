<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\MatchAdShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering match advertisement.
 *
 * @Shortcode(
 *   id = "match-ad",
 *   title = @Translation("Match Ad"),
 *   description = @Translation("Builds a match advertisement")
 * )
 */
class MatchAdShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        $modes = $this->process_modes($attributes);

        $attributes = $this->getAttributes(array(
            'type' => '',
            'title' => '',
            'subtitle' => '',
            'modes' => '',
            'details' => ''
        ),
            $attributes
        );

        if ($attributes['type'] == '')
        {
            $theme = 'shortcode_match_ad';
        }
        else
        {
            $theme = 'shortcode_match_ad_' . $attributes['type'];
        }

        $output = array(
            '#theme' => $theme,
            '#title'=> $attributes['title'],
            '#subtitle'=> $attributes['subtitle'],
            '#modes' => $modes,
            '#details' => $attributes['details'],
            '#text' =>  $text,
        );

        return $this->render($output);
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[match-ad (type="type" background_image="image.png" title="Page header title")] Other shortcode or HTML content here [/match-ad]') . '</strong> ';
        $output[] = $this->t('Renders header for sport page') . '</p>';

        return implode(' ', $output);
    }

    /**
     * Process modes to match a single multidimensional array
     */
    private function process_modes($attributes) {
        $raw_modes = [];
        $modes = [];

        foreach ($attributes as $key => $value)
        {
            if (substr_count($key, 'modes') > 0)
            {
                $mode = explode('_', $key);
                $mode_index = $mode[1];
                $mode_key = $mode[2];
                $raw_modes[$mode_index][] = [
                    $mode_key => $value
                ];
            }
        }

        foreach ($raw_modes as $key => $value)
        {
            $modes[] = $this->flatten($value);
        }

        return $modes;
    }

    /**
     * Removes levels from an array
     */
    private function flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a,$b) use (&$return) { $return[$b] = $a; });
        return $return;
    }

}
