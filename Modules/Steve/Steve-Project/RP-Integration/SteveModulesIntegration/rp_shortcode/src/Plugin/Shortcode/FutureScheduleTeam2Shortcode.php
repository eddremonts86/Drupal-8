<?php
/**
 * @file
 * Contains \Drupal\rp_shortcode\Plugin\Shortcode\FutureScheduleTeam2Shortcode.
 */

namespace Drupal\rp_shortcode\Plugin\Shortcode;

use Drupal\node\Entity\Node;
use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;

/**
 * Provides a shortcode for rendering calendars.
 *
 * @Shortcode(
 *   id = "futureschedule_team2",
 *   title = @Translation("Event Team 2"),
 *   description = @Translation("Returns name of second event team.")
 * )
 */

class FutureScheduleTeam2Shortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

        if($node = \Drupal::routeMatch()->getParameter('node')){
            if($node->type->target_id == 'events'){
                $id = $node->id();
                $node = $node->toArray();
                $controllerObj = new SteveFrontendControler(); 

                $participants = $controllerObj->getParticipant($node["field_event_participants"], $node["field_events_properties"]);
                $participant = array_slice($participants, 1, 1);
                $participant = array_shift($participant);

                $fromDate = $node['field_event_date'][0]['value'];
                $endDate = strtotime('today', $fromDate);
                $endDate = strtotime('+ 3 day', $endDate);

                $channel = $controllerObj->getChannelTaxonomyId();
                $ids = \Drupal::entityQuery('node')
                ->condition('status', 1)
                ->condition('promote', 1)
                ->condition('type', 'events')
                ->condition('field_event_date', $fromDate, '>=')
                ->condition('field_event_date', $endDate, '<=')
                ->condition('field_event_participants', $participant['id'])
                ->condition('field_event_channels', $channel, '=')
                ->sort('field_event_date', 'ASC')
                ->sort('field_event_tournament', 'ASC');

                $ids = $ids->execute();

                foreach ($ids as $index => $eventId) {
                    if($eventId == $id){
                        unset($ids[$index]);
                        break;
                    }
                }

                if($ids){
                    $all_nodes = $controllerObj->getNodes($ids);
                    $nodelist = $controllerObj->getScheduleFormat($all_nodes);
                    $tree = $controllerObj->getTree($nodelist, "Y-m-d");

                    $build = [
                        '#theme' => 'shortcode_schedule_panel',
                        '#tags' => $tree,
                        '#colors' => $controllerObj->getColors(),
                    ];

                    return $this->render($build);
                }else{
                    return 'no events';
                }
            }
        }

        return 'futureschedule_league';
    }

    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
        $output = array();
        $output[] = '<p><strong>' . $this->t('[futureschedule_team2][/futureschedule_team2]') . '</strong> ';
        $output[] = $this->t('Returns name of second event team.') . '</p>';

        return implode(' ', $output);
    }

}
