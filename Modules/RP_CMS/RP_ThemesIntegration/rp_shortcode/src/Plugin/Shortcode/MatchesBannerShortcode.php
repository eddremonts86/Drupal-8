<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\MatchesBannerShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering page matches banner.
 *
 * @Shortcode(
 *   id = "matches-banner",
 *   title = @Translation("Page Matches Banner"),
 *   description = @Translation("Builds a banner with matches listed")
 * )
 */
class MatchesBannerShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        $attributes = $this->getAttributes(array(
            'count' => '',
            'background_image' => '',
            'title' => '',
            'subtitle' => '',
        ),
            $attributes
        );

        $output = array(
            '#theme' => 'shortcode_matches_banner',
            '#count'=> $attributes['count'],
            '#background_image'=> $attributes['background_image'],
            '#title' => $attributes['title'],
            '#subtitle' => $attributes['subtitle'],
        );

        return $this->render($output);
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[matches-banner count="3" (background_image="image.png" title="Page header title" subtitle="Some more long text")][/matches-banner]') . '</strong> ';
        $output[] = $this->t('Renders matches list banner for pages') . '</p>';

        return implode(' ', $output);
    }

}
