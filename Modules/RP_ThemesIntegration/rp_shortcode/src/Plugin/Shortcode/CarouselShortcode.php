<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\CarouselShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering carousels.
 *
 * @Shortcode(
 *   id = "carousel",
 *   title = @Translation("Carousel"),
 *   description = @Translation("Builds a carousel")
 * )
 */
class CarouselShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        $attributes = $this->getAttributes(array(
            'type' => '',
            'background_image' => '',
            'title' => '',
            'count' => '3',
        ),
            $attributes
        );

        if ($attributes['type'] == '')
        {
            $theme = 'shortcode_carousel';
        }
        else
        {
            $theme = 'shortcode_carousel_' . $attributes['type'];
        }

        $output = array(
            '#theme' => $theme,
            '#title'=> $attributes['title'],
            '#background_image'=> $attributes['background_image'],
            '#count'=> $attributes['count']
        );

        return $this->render($output);
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[carousel type="type" (background_image="image.png" title="A title" count="Number of slides")][/carousel]') . '</strong> ';
        $output[] = $this->t('Renders a carousel') . '</p>';

        return implode(' ', $output);
    }

}
