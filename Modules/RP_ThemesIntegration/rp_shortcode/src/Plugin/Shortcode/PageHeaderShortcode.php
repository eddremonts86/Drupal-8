<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\PageHeaderShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Provides a shortcode for rendering page headers.
 *
 * @Shortcode(
 *   id = "page-header",
 *   title = @Translation("Page Header"),
 *   description = @Translation("Builds a header for pages")
 * )
 */
class PageHeaderShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        $attributes = $this->getAttributes(array(
            'type' => '',
            'background_image' => '',
            'title' => '',
            'subtitle' => '',
            'p1' => '',
            'p2' => '',
        ),
            $attributes
        );

        if ($attributes['type'] == '')
        {
            $theme = 'shortcode_page_header';
        }
        else
        {
            $theme = 'shortcode_page_header_' . $attributes['type'];
        }

        $output = array(
            '#theme' => $theme,
            '#title'=> $attributes['title'],
            '#subtitle' => $attributes['subtitle'],
            '#background_image'=> $attributes['background_image'],
            '#p1'=> $attributes['p1'],
            '#p2'=> $attributes['p2'],
            '#text' =>  $text,
        );

        return $this->render($output);
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[page-header type="type" (background_image="image.png" title="Page header title" subtitle="Page header subtitle" p1="Paragraph" p2="Paragraph")] Other shortcode or HTML content here [/page-header]') . '</strong> ';
        $output[] = $this->t('Renders header for sport page') . '</p>';

        return implode(' ', $output);
    }

}
