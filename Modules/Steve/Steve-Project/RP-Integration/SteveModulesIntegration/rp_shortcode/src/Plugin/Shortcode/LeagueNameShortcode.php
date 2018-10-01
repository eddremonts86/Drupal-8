<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\LeagueNameShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;

/**
 * Provides a shortcode for rendering calendars.
 *
 * @Shortcode(
 *   id = "leaguename",
 *   title = @Translation("League Name"),
 *   description = @Translation("Returns event league name.")
 * )
 */
class LeagueNameShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        if($node = \Drupal::routeMatch()->getParameter('node')){
            if($node->type->target_id == 'events'){
                $controllerObj = new SteveFrontendControler();
                    $tournament = $controllerObj->getTaxonomyByCriterio([
                        'vid' => 'sport',
                        'tid' => $node->field_event_tournament->target_id,
                    ], 0);

                return $tournament->name->value;
            }
        }

        return 'LeagueName';
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[leaguename][/leaguename]') . '</strong> ';
        $output[] = $this->t('Returns event league name.') . '</p>';

        return implode(' ', $output);
    }

}
