<?php

namespace Drupal\rp_cms_steve_integration_se_fodbald\Controller;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\rp_client_base\Controller\SteveFrontendControler;

/**
 * Class SeLiveFodbaldController.
 */
Class SeLiveFodbaldController extends SteveFrontendControler
{

    public function seFodbaldHomePage()
    {
        return array(
            '#theme' => 'sefodbaldhomepage',
        );
    }

    public function seFodbaldProvidersPage()
    {
        return array(
            '#theme' => 'sefodbaldproviderspage',
        );
    }

    public function seFodbaldProgramPage()
    {
        return array(
            '#theme' => 'sefodbaldprogrampage',
        );
    }

    public function seFodbaldLeaguesPage()
    {
        return array(
            '#theme' => 'sefodbaldleaguespage',
        );
    }

    public function seFodbaldMatchPage()
    {
        return array(
            '#theme' => 'sefodbaldmatchpage',
        );
    }

    public function seFodbaldLeaguePage()
    {
        return array(
            '#theme' => 'sefodbaldleaguepage',
        );
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function SeFodbaldMenuLeaguesLink(&$menu_item)
    {
        $leagues = $this->getLeaguesList(15);
        if ($leagues) {
            foreach ($leagues as $region) {
                foreach ($region['list'] as $league) {
                    $menu_item['childs'][] = [
                        'name' => $league['name'],
                        'link' => $league['link']
                    ];
                }
            }
        }
    }

    public function getSeFodbaldBreadcrumb()
    {
        $data = [];
        $links = \Drupal::service('breadcrumb')->build(\Drupal::routeMatch())->getLinks();

        if ($links) {
            $elements = explode('.', implode('.d.', array_keys($links)));

            foreach ($elements as $element) {
                if ($element !== 'd' && isset($links[$element])) {
                    $text = $links[$element]->getText();

                    if (!is_string($text)) {
                        $text = $text->render();
                    }

                    $data[] = [
                        'text' => $text,
                        'type' => 'link',
                        'url' => $links[$element]->getUrl()->toString()
                    ];
                } else if ($element === 'd') {
                    $data[] = [
                        'type' => 'delimiter'
                    ];
                }
            }
        }

        return $data;
    }

    public function getSeFodbaldLeagueInfo()
    {
        $data = [];
        $term = $this->getTaxonomyTermByUrl();
        $data['name'] = $term->name->value;
        $data['description'] = $term->getDescription();
        $data['link'] = $this->getTaxonomyAlias($term->id());
        if (isset($term->field_logo->target_id)) {
            $data['image'] = $this->getImgUrl($term->field_logo->target_id);
        }
        return $data;
    }

    public function getFormatEvent($node, $providers = FALSE)
    {
        $event = [];
        $taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

        $event['date'] = $node->field_event_date->value;
        $event['name'] = $node->title->value;
        $event['link'] = $this->getNodeAlias($node->id());
        $event['description'] = $this->getShortcode($node->body->value);

        foreach ($node->get('field_event_participants')->getValue() as $key => $team) {
            $side = $key % 2 == 0 ? 'left' : 'right';

            $term = $taxonomyManager->load($team['target_id']);

            $event['teams'][$side]['name'] = $term->name->value;
            $event['teams'][$side]['description'] = $term->getDescription();
            $event['teams'][$side]['link'] = $this->getTaxonomyAlias($term->id());

            if (isset($term->field_participant_logo->target_id)) {
                $event['teams'][$side]['image'] = $this->getImgUrl($term->field_participant_logo->target_id);
            }
        }

        if ($providers) {
            $event['providers'] = $this->getSeFodbaldProviders($node);
        }

        return $event;
    }

    public function loadEvents($params, $range = NULL, $pager = NULL)
    {
        $sport = $this->getSport(2, 'api');

        $query = \Drupal::entityQuery('node')
            ->condition('status', 1)
            ->condition('promote', 1)
            ->condition('field_events_sport', $sport['sportDrupalId'])
            ->condition('type', 'events');

        foreach ($params as $condition) {
            $query->condition($condition['field'], $condition['value'], $condition['operator']);
        }

        if ($range) {
            $query->range(0, $range);
        }

        $query->sort('field_event_date', 'ASC');
        $query->sort('field_event_tournament', 'ASC');

        if ($pager) {
            $query->pager($pager);
        }

        return $query->execute();
    }

    public function getEvents($source = null, $range = 3, $loadProviders = false)
    {
        $data = [];
        $now = time();
        $nodes = [];
        $nodesManager = \Drupal::entityTypeManager()->getStorage('node');

        switch ($source) {
            case 'node':
                $nodes[] = $this->getNodeByUrl(1);
                break;
            case 'term':
                if ($term = $this->getTaxonomyTermByUrl()) {
                    $params = [
                        ['field' => 'field_event_tournament', 'value' => $term->id(), 'operator' => "="],
                        ['field' => 'field_event_date', 'value' => $now, 'operator' => '>=']
                    ];

                    $nodes = $nodesManager->loadMultiple($this->loadEvents($params, $range));
                }
                break;
            case 'league':
                if ($node = $this->getNodeByUrl(1)) {
                    if (isset($node->field_event_tournament->target_id)) {
                        $params = [
                            ['field' => 'field_event_tournament', 'value' => $node->field_event_tournament->target_id, 'operator' => "="],
                            ['field' => 'field_event_date', 'value' => $now, 'operator' => '>=']
                        ];
                        $nodes = $nodesManager->loadMultiple($this->loadEvents($params, $range));
                    }
                }
                break;
            default:
                $nodes = $nodesManager->loadMultiple($this->loadEvents([['field' => 'field_event_date', 'value' => $now, 'operator' => '>=']], $range));
                break;
        }

        if ($nodes) {
            foreach ($nodes as $node) {
                if ($node instanceof \Drupal\node\NodeInterface) {
                    $data[$node->id()] = $this->getFormatEvent($node, $loadProviders);
                }
            }
        }

        return $data;
    }

    public function getLeaguesList($limit = 0)
    {
        $list = [];
        $manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
        $query = \Drupal::database()
            ->select('taxonomy_term_field_data', 't');
        $query->join('taxonomy_term_hierarchy', 'h', 'h.tid = t.tid');
        $query->join('taxonomy_term__field_weight', 'w', 'w.entity_id = t.tid');
        $query->fields('t', ['tid'])
            ->condition('t.vid', 'sport')
            ->condition('t.default_langcode', 1);
        $query->condition('w.field_weight_value', $limit, '<=');
        $leagues = $query->orderBy('w.field_weight_value')->execute()->fetchAll();
        if ($leagues) {
            foreach ($leagues as $league) {
                if ($limit && count($list) == $limit) {
                    break;
                }
                $league = $manager->load($league->tid);
                $list[$league->id()]['list'][$league->id()]['id'] = $league->id();
                $list[$league->id()]['list'][$league->id()]['name'] = $league->name->value;
                $list[$league->id()]['list'][$league->id()]['link'] = $this->getTaxonomyAlias($league->id());
                $list[$league->id()]['list'][$league->id()]['img'] = $this->getImgUrl($league->field_logo->target_id);
            }
        }
        return $list;
    }

    public function sortTaxonomyTreeByWeight($vid, $parent = 0, $maxWeigh = 0)
    {

        if (!db_table_exists('taxonomy_term__field_weight')) {
            return [];
        }

        $query = \Drupal::database()
            ->select('taxonomy_term_field_data', 't');
        $query->join('taxonomy_term_hierarchy', 'h', 'h.tid = t.tid');
        $query->join('taxonomy_term__field_weight', 'w', 'w.entity_id = t.tid');
        $query->fields('t', ['tid'])
            ->condition('t.vid', $vid)
            //   ->condition('h.parent', $parent)
            ->condition('t.default_langcode', 1);
        if (!$maxWeigh == 0) {
            $query->condition('w.field_weight_value', $maxWeigh, '<=');
        }
        return $query->orderBy('w.field_weight_value')->execute()->fetchAll();
    }


    public function getSeFodbaldProviders($node = null)
    {
        $data = [];
        $providers = [];
        $taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

        if ($node) {
            $tids = [];

            foreach ($node->get('field_event_stream_provider')->getValue() as $tid) {
                $tids[] = $tid['target_id'];
            }

            if ($tids) {
                $providers = $taxonomyManager->loadMultiple($tids);
            }

        } else {
            $providers = $this->getStreamProviders();


        }

        if ($providers) {
            foreach ($providers as $provider) {
                $data[$provider->id()]['id'] = $provider->id();
                $data[$provider->id()]['name'] = $provider->name->value;
                $data[$provider->id()]['sponsored'] = $provider->field_stream_provider_sponsor->value;
                $data[$provider->id()]['rating'] = rand(1, 5);
                $data[$provider->id()]['link'] = 'http://google.com';
                $data[$provider->id()]['review'] = 'bet365 viser kampe fra bl.a. La Liga, Serie A, Bundesligaen & mange flere…';
                $data[$provider->id()]['quality'] = 'Billede og lyd er i top hos bet365. Kvaliteten sikrer en god fodboldoplevelse.';
                $data[$provider->id()]['price'] = 'Du skal have penge på kontoen for at kunne livestreame';
                $data[$provider->id()]['leagueandmatch'] = 'Unibet tilbyder en bred vifte af live streamede fodbold. Deres sortiment dækker kampe fra Serie A, Bundesliga og La Liga';
                $data[$provider->id()]['image'] = $this->getImgUrl($provider->field_streamprovider_logo->target_id);

                if (!$provider->get('field_stream_disclaimer')->isEmpty()) {
                    $data[$provider->id()]['disclaimer'] = $provider->field_stream_disclaimer->value;
                }
            }
        }

        return $data;
    }

    public function debug($responseObj)
    {
        \Drupal::logger('rp_cms_steve_integration_se_fodbald')->warning('<pre><code>' . print_r($responseObj, TRUE) . '</code></pre>');
    }
}
