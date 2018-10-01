<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\Team2Shortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;

/**
 * Provides a shortcode for rendering calendars.
 *
 * @Shortcode(
 *   id = "team_2",
 *   title = @Translation("Event Team 2"),
 *   description = @Translation("Returns name of second event team.")
 * )
 */
class Team2Shortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        if($node = \Drupal::routeMatch()->getParameter('node')){
            if($node->type->target_id == 'events'){
                $node = $node->toArray();
                $controllerObj = new SteveFrontendControler();
                $participants = $controllerObj->getParticipant($node["field_event_participants"], $node["field_events_properties"]);

                $participant = array_slice($participants, 1, 1);
                $participant = array_shift($participant);

                return $participant['name'];
            }
        }

        return 'team_2';
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[team_2][/team_2]') . '</strong> ';
        $output[] = $this->t('Returns name of second event team.') . '</p>';

        return implode(' ', $output);
    }

}
