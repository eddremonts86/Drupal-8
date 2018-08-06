<?php

namespace Drupal\rp_cms_steve_integration\Controller;

use Drupal\rp_cms_steve_integration\Controller\SteveBaseControler;

/**
 * Class SteveBackendControler.
 */
class SteveBackendControler extends SteveBaseControler
{

    /**
     * __construct.
     *
     * @return string
     *   Return Hello string.
     */


    public function getSportPages()
    {
        $obj = ['type' => 'sport'];
        $sportNodeList = $this->getNodeByCriterio($obj,1);
        $nodesFormat = array();
        foreach ($sportNodeList as $sport) {
            $nodesFormat[$sport->field_sport_sport->target_id] = $sport->title->value;
        }
        return $nodesFormat;
    }

    public function getSchedulebydate($sport, $date, $pagesNumber = 25)
    {
        set_time_limit(120);
        $fromDate = strtotime($date);
        $endDate = strtotime('+' . 1 . ' day', strtotime($date));
        $ids = \Drupal::entityQuery('node')
            ->condition('status', 1)
            ->condition('promote', 1)
            ->condition('type', 'events')
            ->condition('field_events_sport', $sport)
            ->condition('field_event_date', $fromDate, '>')
            ->condition('field_event_date', $endDate, '<')
            ->pager($pagesNumber)
            ->sort('field_event_date', 'DESC');
            //->sort('field_event_tournament', 'ASC');
        $all_nodes = $this->getNodes($ids->execute());
        return $this->getScheduleFormat($all_nodes);
    }

    public function getAllScheduleBySport($sport, $pagesNumber = 25)
    {
        set_time_limit(120);
        $ids = \Drupal::entityQuery('node')
            ->condition('status', 1)
            ->condition('promote', 1)
            ->condition('type', 'events')
            ->condition('field_events_sport', $sport)
            ->pager($pagesNumber)
            ->sort('field_event_date', 'DESC');
            //->sort('field_event_tournament', 'ASC');
        $all_nodes = $this->getNodes($ids->execute());
        return $this->getScheduleFormat($all_nodes);
    }

    public function renderTable($results)
    {
        $data = array();
        if (is_array($results)) {
            foreach ($results as $result) {
                $data[] = array(
                    'EventTitle' => ['#markup' => $this->t('<a href="/node/' . $result['nodeId'] . '">' . $result['title'] . '</a>')],
                    'Sport' => $result['sportname'],
                    'Leage' => $result['eventTournamentName'],
                    'eventDate' => date("d-m-Y:h:i a", $result['eventDate']),
                    'OPERATIONS' => ['#markup' => $this->t('<a href="/node/' . $result['nodeId'] . '/edit?destination=/admin/content" class="button js-form-submit form-submit">edit</a>')]
                );
            }
        }
        return $data;
    }

}
