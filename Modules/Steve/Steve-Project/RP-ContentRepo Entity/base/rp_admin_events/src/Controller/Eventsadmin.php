<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 3/20/18
 * Time: 10:55 AM
 */

namespace Drupal\rp_admin_events\Controller;

use Drupal\rp_admin_events\Controller\SteveBaseControler;
use Drupal\rp_game\GameListBuilder;

class Eventsadmin extends SteveBaseControler
{



    public function getAllEvents($sport, $pagesNumber = 50)
    {
        set_time_limit(120);
        $ids = \Drupal::entityQuery('game')
            ->pager($pagesNumber);
        $entities = $ids->execute();
        if ($entities) {
            $entity_manager = \Drupal::entityTypeManager();
            $all_entities = $entity_manager->getStorage('game')->loadMultiple($entities);
            return $this->getEntitiesListeFormated($all_entities, true);
        } else {
            return "We don't have any data... Sorry";
        }

    }
    public function getEvent($entityID)
    {
        set_time_limit(120);
        $ids = \Drupal::entityQuery('game_info')->condition('field_game_info_game', $entityID);
        $entities = $ids->execute();
        if ($entities) {
            $entity_manager = \Drupal::entityTypeManager();
            $all_entities = $entity_manager->getStorage('game_info')->loadMultiple($entities);
            return $this->getEntitiesListeFormated($all_entities);
        } else {
            return "We don't have any data... Sorry";
        }

    }

    public function getSport($id)
    {
        $entity_manager = \Drupal::entityTypeManager();
        $entities = $entity_manager->getStorage('sport')->load($id);
        return $entities->name->value;
    }
    public function getTournament($id)
    {
        $entity_manager = \Drupal::entityTypeManager();
        $entities = $entity_manager->getStorage('competition')->load($id);
        return $entities->name->value;
    }
    public function getSite($id)
    {
        $entity_manager = \Drupal::entityTypeManager();
        $entities = $entity_manager->getStorage('site')->load($id);
        return $entities->name->value;
    }
    public function getRegion($id)
    {
        $entity_manager = \Drupal::entityTypeManager();
        $entities = $entity_manager->getStorage('region')->load($id);
        return $entities->name->value;
    }
    public function getlegauge($id)
    {
        $entity_manager = \Drupal::entityTypeManager();
        $entities = $entity_manager->getStorage('language_content')->load($id);
        return $entities->name->value;
    }
    /*------------------------------------------------------------*/

    public function renderTable($results)
    {
        $data = array();
        if (is_array($results)) {
            foreach ($results as $result) {
                $data[] = array(
                    'EventName' => ['#markup' => $this->t('<a href="/node/' . $result['nodeId'] . '">' . $result['title'] . '</a>')],
                    'countRelated' => $result['countRelated'],
                    'Sport' => $result['sportname'],
                    'Leage' => $result['eventTournamentName'],
                    'eventDate' => date("d-m-Y:h:i a", $result['eventDate']),
                    'OPERATIONS' => ['#markup' => $this->t('<a href="/admin/rp/event/' . $result['nodeId'] . '/' . $result['title'] . '" class="button js-form-submit form-submit">edit</a>')]
                );
            }
        }
        return $data;
    }
    public function renderTableEvent($results)
    {
        $data = array();
        if (is_array($results)) {
            foreach ($results as $result) {
                $data[] = array(
                    'EventName' => ['#markup' => $this->t('<a href="/admin/rp/entity-content/game_info/' . $result['nodeId'] . '">' . $result['title'] . '</a>')],
                    'Sport' => $result['site'],
                    'Leage' => $result['region'],
                    'eventDate' => $result['languge'],
                    'OPERATIONS' => ['#markup' => $this->t('<a href="/admin/rp/entity-content/game_info/' . $result['nodeId'] . '/edit" class="button js-form-submit form-submit">edit</a>')]

                );
            }
        }
        return $data;
    }
    public function getEntitiesListeFormated($all_entities, $game = false)
    {
        if ($all_entities) {
            $formatedEntities = array();
            foreach ($all_entities as $entity) {
                if ($game) {
                    $ids = \Drupal::entityQuery('game_info')->condition('field_game_info_game', $entity->vid->value);
                    $entities = $ids->count()->execute();
                }
                else {
                    $site = $entity->field_game_info_site ->target_id ? $this->getSite($entity->field_game_info_site ->target_id) : '' ;
                    $region = $entity->field_game_info_region->target_id ? $this->getRegion($entity->field_game_info_region->target_id) : '';
                    $languge = $entity->field_game_info_language->target_id ? $this->getlegauge($entity->field_game_info_language->target_id) : '';
                }
                $formatedEntities[] = array(
                    'nodeId' => $entity->vid->value,
                    'title' => $entity->name->value,
                    'sportname' => $entity->field_game_sport->target_id ? $this->getSport($entity->field_game_sport->target_id) : '',
                    'eventDate' => $entity->field_game_date->value,
                    'eventTournamentName' => $entity->field_game_competition->target_id ? $this->getTournament($entity->field_game_competition->target_id) : '',
                    'countRelated' => $entities ? $entities : '',
                    'site'=> $site ? $site : '',
                    'region'=> $region ? $region : '',
                    'languge'=> $languge ? $languge : ''
                );

            }
            return $formatedEntities;
        } else {
            \Drupal\Core\Form\drupal_set_message($this->t('no info'));
        }


    }

}


