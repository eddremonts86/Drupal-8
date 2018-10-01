<?php

namespace Drupal\rp_repo\Controller\oldVersion;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_repo\RepoService;
use Drupal\rp_api\RepoGeneralGetInfo;
use Drupal\Core\Routing\RoutingEvents;
use \Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Class ImportRepoData.
 *
 * @package Drupal\rp_repo\Controller
 */
class ImportRepoData extends ControllerBase
{
    public function importRepoData()
    {
        $rpClient = RepoService::getClient();
        $sites = $rpClient->getSites();
        return $sites;
    }

    public function getSchedule($range = 0, $sportid)
    {
        $fromDate = strtotime(date('Y-m-d'));
        $sport = \Drupal\rp_repo\Controller\oldVersion\RepoGeneralGetInfo::getTaxonomyByAPIID('sport_'.$sportid);
        $query = \Drupal::entityQuery('node');
        $query->condition('type', 'events');
        $query->condition('field_events_sport', $sport->id());
        $query->condition('status', 1);
        $query->condition('field_event_date', $fromDate, '<');
        $query->sort('field_event_date', 'ASC');
        if ($range != 0) { $query->range(0, $range); }
        $ids = $query->execute();
        $all_nodes = $this->getNodes($ids);
        $all_nodes_format = array();
        foreach ($all_nodes as $nodes) {
            echo $nodes["title"][0]["value"]. ' - '.date('Y-m-d',$nodes["field_event_date"][0]["value"])."\n";
            $all_nodes_format [] = $nodes["title"][0]["value"];
        }
        return $all_nodes_format;
    }

    public function getStream()
    {
        $query = \Drupal::entityQuery('node');
        $query->condition('status', 1);
        $query->condition('type', 'stream_provider');
        $query->sort('field_properties_rating', 'ASC');
        $ids = $query->execute();
        $all_nodes = $this->getNodes($ids);
        return $all_nodes;
    }

    public function getTaxonomy($name)
    {
        $taxonomy = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties(['name' => $name]);
        return $taxonomy;
    }

    public function getNodes($ids)
    {
        $all_nodes = [];
        foreach ($ids as $id) {
            $node = Node::load($id);
            $all_nodes [] = $node->toArray();
        }
        return $all_nodes;
    }

    public function createSite($site, $datachanels, $datalang, $datareg)
    {
        return true;
    }

    public function createLang($datalang)
    {
        return true;
    }

    public function createChannels($datachanels)
    {
        return true;
    }

    public function createRegions($datareg)
    {
        return true;
    }
}
