<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\StreamProvidersShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering match advertisement.
 *
 * @Shortcode(
 *   id = "stream-providers",
 *   title = @Translation("Stream Providers"),
 *   description = @Translation("Builds an stream providers list")
 * )
 */
class StreamProvidersShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        $attributes = $this->getAttributes(array(
            'type' => '',
            'title' => '',
            'size' => '',
            'background_image' => '',
        ),
            $attributes
        );

        if ($attributes['type'] == '')
        {
            $theme = 'shortcode_stream_providers';
        }
        else
        {
            $theme = 'shortcode_stream_providers_' . $attributes['type'];
        }

        if ($attributes['size'] != '')
        {
            $theme = $theme . '_' . $attributes['size'];
        }

        $output = array(
            '#theme' => $theme,
            '#title'=> $attributes['title'],
            '#background_image'=> $attributes['background_image'],
        );

        return $this->render($output);
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[stream-providers (type="type" size="size" title="Page header title" background_image="background_image.ext")][/stream-providers]') . '</strong> ';
        $output[] = $this->t('Renders stream providers list') . '</p>';

        return implode(' ', $output);
    }

}
