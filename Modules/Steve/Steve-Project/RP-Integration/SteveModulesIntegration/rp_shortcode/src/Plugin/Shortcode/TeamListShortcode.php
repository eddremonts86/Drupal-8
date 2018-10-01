<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\TeamListShortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;

/**
 * Provides a shortcode for rendering calendars.
 *
 * @Shortcode(
 *   id = "teamlist",
 *   title = @Translation("Event Team List"),
 *   description = @Translation("Builds a event team list.")
 * )
 */
class TeamListShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        if($node = \Drupal::routeMatch()->getParameter('node')){
            if($node->type->target_id == 'events'){
                $node = $node->toArray();
                $controllerObj = new SteveFrontendControler();
                $participants = $controllerObj->getParticipant($node["field_event_participants"], $node["field_events_properties"]);

                $output = array(
                    '#theme' => 'shortcode_team_list',
                    '#participants'=> $participants,
                );

                return $this->render($output);
            }
        }

        return 'teamlist';
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[teamlist][/teamlist]') . '</strong> ';
        $output[] = $this->t('Builds a event team list.') . '</p>';

        return implode(' ', $output);
    }

}
