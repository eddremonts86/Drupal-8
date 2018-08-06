<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Controller;

use Drupal\Core\Url;
use Drupal\rp_client_base\Controller\SteveFrontendControler;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Component\Utility\Unicode;


/**
 * Class LiveFodbaldController.
 */
Class LiveFodbaldController extends SteveFrontendControler {

  public function fodbaldHomePage() {
    return [
      '#theme' => 'fodbaldhomepage',
    ];
  }

  public function fodbaldProgramPage() {
    return [
      '#theme' => 'fodbaldprogrampage',
    ];
  }

  public function fodbaldProvidersPage() {
    return [
      '#theme' => 'fodbaldproviderspage',
    ];
  }

  public function fodbaldLeaguesPage() {
    return [
      '#theme' => 'fodbaldleaguespage',
    ];
  }

  public function fodbaldTeamsPage() {
    return [
      '#theme' => 'fodbaldteamspage',
    ];
  }

  public function fodbaldPreviewsPage() {
    return [
      '#theme' => 'fodbaldpreviewspage',
    ];
  }

  public function fodbaldMatchPage() {
    return [
      '#theme' => 'fodbaldmatchpage',
    ];
  }

  public function fodbaldLeaguePage() {
    return [
      '#theme' => 'fodbaldleaguepage',
    ];
  }

  public function fodbaldTeamPage() {
    return [
      '#theme' => 'fodbaldteampage',
    ];
  }

  public function generateEventArticleAlias($entity) {
    $token = \Drupal::service('token');
    $moduleHandler = \Drupal::service('module_handler');
    $aliasCleaner = \Drupal::service('pathauto.alias_cleaner');
    $tokenEntityMapper = \Drupal::service('token.entity_mapper');
    $aliasUniquifier = \Drupal::service('pathauto.alias_uniquifier');

    $pattern = \Drupal::entityTypeManager()
      ->getStorage('pathauto_pattern')
      ->load('event_with_article');

    if (empty($pattern)) {
      return NULL;
    }

    $source = '/' . $entity->toUrl()->getInternalPath();
    $langcode = $entity->language()->getId();

    if ($langcode == LanguageInterface::LANGCODE_NOT_APPLICABLE) {
      $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED;
    }

    $data = [
      $tokenEntityMapper->getTokenTypeForEntityType($entity->getEntityTypeId()) => $entity,
    ];

    $context = [
      'module' => $entity->getEntityType()->getProvider(),
      'op' => 'insert',
      'source' => $source,
      'data' => $data,
      'bundle' => $entity->bundle(),
      'language' => &$langcode,
    ];

    $moduleHandler->alter('pathauto_pattern', $pattern, $context);

    $alias = $token->replace($pattern->getPattern(), $data, [
      'clear' => TRUE,
      'callback' => [$aliasCleaner, 'cleanTokenValues'],
      'langcode' => $langcode,
      'pathauto' => TRUE,
    ], new BubbleableMetadata());

    $pattern_tokens_removed = preg_replace('/\[[^\s\]:]*:[^\s\]]*\]/', '', $pattern->getPattern());
    if ($alias === $pattern_tokens_removed) {
      return NULL;
    }

    $alias = $aliasCleaner->cleanAlias($alias);

    $context['source'] = &$source;
    $context['pattern'] = $pattern;
    $moduleHandler->alter('pathauto_alias', $alias, $context);

    if (!Unicode::strlen($alias)) {
      return NULL;
    }

    $original_alias = $alias;
    $aliasUniquifier->uniquify($alias, $source, $langcode);

    return [
      'alias' => $alias,
      'source' => $source,
      'langcode' => $langcode,
    ];
  }

  public function LiveFodbaldScheduleFormatModificator(&$data, $node) {
    $data['LiveFodbaldAlias'] = $this->getFodbaldEventAlias($node['nid'][0]['value']);
  }

  public function getFodbaldEventAlias($id, $preview = NULL) {
    $path = FALSE;
    $database = \Drupal::database();
    $nodesManager = \Drupal::entityTypeManager()->getStorage('node');
    $node = $nodesManager->load($id);

    if (!$node) {
      return NULL;
    }

    $aliases = $database->select('url_alias', 'ua')
      ->fields('ua', ['source', 'alias', 'pid'])
      ->condition('source', "%" . $database->escapeLike('/node/' . $id) . "%", 'LIKE')
      ->orderBy('pid', 'DESC')
      ->execute()
      ->fetchAll();

    if (!$aliases) {
      return '/node/' . $id;
    }

    if ($node->field_event_article->value && $preview) {
      foreach ($aliases as $alias) {
        if (explode('/', $alias->alias)[1] == 'optakter') {
          $path = $alias->alias;
          break;
        }
      }

      if ($path) {
        return $path;
      }

      $path = array_shift($aliases);

      return $path->alias;
    }

    foreach ($aliases as $alias) {
      if (explode('/', $alias->alias)[1] != 'optakter') {
        $path = $alias->alias;
        break;
      }
    }

    if ($path) {
      return $path;
    }

    return '/node/' . $id;
  }

  public function formatedProviders() {
    $providers = $this->getStreamProviders();

    $formatted = [];

    foreach ($providers as $provider) {
      $data = [];
      $data['name'] = $provider->name->value;
      $data['image_1'] = 'modules/custom/RP_CMS/RP_ThemesIntegration/rp_cms_steve_integration_live_fodbald/src/images/provider-logo-1.jpg';
      $data['image_2'] = 'modules/custom/RP_CMS/RP_ThemesIntegration/rp_cms_steve_integration_live_fodbald/src/images/bet365.png';
      $data['review'] = ': bet365 viser kampe fra en masse store internationale ligaer. Her får du La Liga, Serie A, 1. Bundesliga & meget mere.';
      $data['price'] = ': Du skal have penge på kontoen for at kunne live streame';
      $data['quality'] = ': Billede og lyd er i top hos bet365. Kvaliteten sikrer en god fodboldoplevelse.';
      $data['step_link_1'] = "/";
      $data['step_link_2'] = "/";
      $data['step_link_3'] = "/";
      $data['link'] = '#';
      $data['sponsored'] = $provider->field_stream_provider_sponsor->value;

      if (!$provider->get('field_stream_disclaimer')->isEmpty()) {
        $data['disclaimer'] = $provider->field_stream_disclaimer->value;
      }

      $formatted[] = $data;
    }

    return $formatted;
  }

  public function getFodbaldSchedulePage($format = "Y-m-d") {
    $time = time();
    $schedule = NULL;

    if ($get = \Drupal::request()->query->get('date')) {
      $time = strtotime("midnight", strtotime($get));
    }

    $schedule = $this->getSchedulePlusTree(0, $format, 1, 0, $this->getSport(2, 'api'), $time, NULL, ['LiveFodbaldScheduleFormatModificator']);

    for ($i = -2; $i < 5; $i++) {
      $timestamp = $time + (86400 * $i);

      $schedule['pager']['days'][$timestamp]['active'] = FALSE;
      $schedule['pager']['days'][$timestamp]['format'] = date("D", $timestamp) . '<br>' . date("d", $timestamp);
      $schedule['pager']['days'][$timestamp]['link'] = \Drupal\Core\Url::fromRoute('<current>', [], ['query' => ['date' => date($format, $timestamp)]]);

      if ($i == 0) {
        $schedule['pager']['days'][$timestamp]['active'] = TRUE;
      }
    }

    $schedule['date'] = date($format, $time);
    $schedule['format'] = $format;
    $schedule['pager']['next'] = \Drupal\Core\Url::fromRoute('<current>', [], ['query' => ['date' => date($format, $time + 86400)]]);
    $schedule['pager']['prev'] = \Drupal\Core\Url::fromRoute('<current>', [], ['query' => ['date' => date($format, $time - 86400)]]);

    return $schedule;
  }

  public function loadMatch($params, $range = NULL, $pager = NULL) {
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

  public function getFodbaldMatchData($loadBy = NULL, $quantity = 1, $loadProviders = FALSE) {

    $nodes = [];
    $events = [];
    $now = time();
    $route = \Drupal::routeMatch()->getRouteName();
    $nodesManager = \Drupal::entityTypeManager()->getStorage('node');
    $taxonomyManager = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term');

    switch ($loadBy) {
      case 'node':
        $nodes[] = $this->getNodeByUrl(1);
        break;
      case 'term':
        if ($term = $this->getTaxonomyTermByUrl()) {
          $params = [];
          if ($term->getVocabularyId() == 'sport') {
            $params = [
              [
                'field' => 'field_event_tournament',
                'value' => $term->id(),
                'operator' => "=",
              ],
              [
                'field' => 'field_event_date',
                'value' => $now,
                'operator' => '>=',
              ],
            ];
          }
          else {
            if ($term->getVocabularyId() == 'participant') {
              $params = [
                [
                  'field' => 'field_event_participants',
                  'value' => $term->id(),
                  'operator' => "=",
                ],
                [
                  'field' => 'field_event_date',
                  'value' => $now,
                  'operator' => '>=',
                ],
              ];
            }
          }

          if ($params) {
            $nodes = $nodesManager->loadMultiple($this->loadMatch($params, $quantity));
          }
        }
        break;
      default:
        $nodes = $nodesManager->loadMultiple($this->loadMatch([
          [
            'field' => 'field_event_date',
            'value' => $now,
            'operator' => '>=',
          ],
        ], $quantity));
        break;
    }

    if ($nodes) {
      foreach ($nodes as $node) {
        if ($node instanceof \Drupal\node\NodeInterface) {

          $uid = $node->getOwnerId();

          if ($uid == 0 && isset($node->revision_uid)) {
            $field = $node->get('revision_uid')->getValue();
            if ($field) {
              $uid = $field[0]['target_id'];
            }
          }

          $user = \Drupal\user\Entity\User::load($uid);

          $events[$node->id()]['matchDate'] = $node->field_event_date->value;
          $events[$node->id()]['matchName'] = $node->title->value;
          $events[$node->id()]['matchLink'] = $this->getFodbaldEventAlias($node->id());
          $events[$node->id()]['matchDescription'] = $this->getShortcode($node->body->value);
          $events[$node->id()]['userId'] = $uid;
          $events[$node->id()]['userName'] = $user->getUsername();
          $events[$node->id()]['userLink'] = '/user/' . $uid;
          $events[$node->id()]['userImage'] = '';


          foreach ($node->get('field_event_participants')
                     ->getValue() as $key => $team) {
            $side = $key % 2 == 0 ? 'left' : 'right';

            $term = $taxonomyManager->load($team['target_id']);

            $events[$node->id()]['matchTeams'][$side]['name'] = $term->name->value;
            $events[$node->id()]['matchTeams'][$side]['description'] = $term->getDescription();
            $events[$node->id()]['matchTeams'][$side]['link'] = $this->getTaxonomyAlias($term->id());

            if (isset($term->field_participant_logo->target_id)) {
              $events[$node->id()]['matchTeams'][$side]['image'] = $this->getImgUrl($term->field_participant_logo->target_id);
            }
          }

          if ($loadProviders) {
            $events[$node->id()]['matchProviders'] = $this->getStreamListFormat($node->get('field_event_stream_provider')
              ->getValue());
          }
        }
      }
    }

    return $events;
  }

  public function getTeamPageInfo() {
    $data = [];
    $term = $this->getTaxonomyTermByUrl();
    $data['name'] = $term->name->value;
    $data['description'] = $term->getDescription();
    $data['body'] = $term->field_participant_content->value;
    $data['link'] = $this->getTaxonomyAlias($term->id());
    if (isset($term->field_participant_logo->target_id)) {
      $data['image'] = $this->getImgUrl($term->field_participant_logo->target_id);
    }
    return $data;
  }

  public function getLeaguePageInfo() {
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

  public function getFodbaldHomeTabs() {
    $data = [];
    $taxonomyManager = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term');

    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', ["sport", "participant"], "IN");

    $or = $query->orConditionGroup();
    $or->condition('field_participant_front', 1);
    $or->condition('field_sport_front', 1);

    $query->condition($or);

    $terms = $taxonomyManager->loadMultiple($query->execute());

    if ($terms) {
      foreach ($terms as $term) {
        $type = $term->getVocabularyId();
        $data[$type][$term->id()]['name'] = $term->name->value;
        $data[$type][$term->id()]['link'] = $this->getTaxonomyAlias($term->id());
        $data[$type][$term->id()]['frontTitle'] = $term->get('field_' . $type . '_front_title')
          ->getValue();
        $data[$type][$term->id()]['frontContent'] = $term->get('field_' . $type . '_front_content')
          ->getValue();
        $data[$type][$term->id()]['sponsorLink'] = $term->get('field_' . $type . '_sponsor_link')
          ->getValue();
      }
    }

    return $data;
  }

  public function getLeaguesList() {
    $list = [];

    $sport = $this->getSport(2, 'api');
    $manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $parent = $manager->loadTree('sport', $sport['sportDrupalId'], 1, FALSE);

    $parent = reset($parent);

    $terms = $this->sortTaxonomyTreeByWeight('sport', $parent->tid);

    foreach ($terms as $term) {
      $term = $manager->load($term->tid);

      $list[$term->id()]['name'] = $term->name->value;
      $list[$term->id()]['list'] = [];
      $leagues = $this->sortTaxonomyTreeByWeight('sport', $term->id());

      if ($leagues) {
        foreach ($leagues as $league) {
          $league = $manager->load($league->tid);
          $list[$term->id()]['list'][$league->id()]['id'] = $league->id();
          $list[$term->id()]['list'][$league->id()]['name'] = $league->name->value;
          $list[$term->id()]['list'][$league->id()]['link'] = $this->getTaxonomyAlias($league->id());
          $list[$term->id()]['list'][$league->id()]['img'] = $this->getImgUrl($league->field_logo->target_id);
        }
      }
    }

    return $list;
  }

  public function sortTaxonomyTreeByWeight($vid, $parent = 0) {

    if (!db_table_exists('taxonomy_term__field_weight')) {
      return [];
    }

    $query = \Drupal::database()
      ->select('taxonomy_term_field_data', 't');
    $query->join('taxonomy_term_hierarchy', 'h', 'h.tid = t.tid');
    $query->join('taxonomy_term__field_weight', 'w', 'w.entity_id = t.tid');
    return $query->fields('t', ['tid'])
      ->condition('t.vid', $vid)
      ->condition('h.parent', $parent)
      ->condition('t.default_langcode', 1)
      ->orderBy('w.field_weight_value')
      ->execute()
      ->fetchAll();
  }

  public function getFodbaldPreview() {
    $data = [];
    $event = $this->getNodeByUrl(1);

    if ($event) {
      if ($event->bundle() == 'events' && $event->field_event_article->value && explode('/', \Drupal::request()
          ->getRequestUri())[1] == 'optakter') {
        $uid = $event->getOwnerId();

        if ($uid == 0 && isset($event->revision_uid)) {
          $field = $event->get('revision_uid')->getValue();
          if ($field) {
            $uid = $field[0]['target_id'];
          }
        }

        $user = \Drupal\user\Entity\User::load($uid);

        $data['name'] = $event->title->value;
        $data['link'] = $this->getFodbaldEventAlias($event->id(), TRUE);
        $data['eventLink'] = $this->getFodbaldEventAlias($event->id());
        $data['description'] = $event->body->value;
        $data['date'] = $event->field_event_date->value;
        $data['userId'] = $uid;
        $data['userName'] = $user->getUsername();
        $data['userLink'] = '/user/' . $uid;
        $data['userImage'] = '';
      }
    }

    return $data;
  }

  public function getFodbaldPreviews($range = NULL, $elements = 10, $slides = NULL) {
    $previews = [];
    $nodesManager = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $this->loadMatch([
      [
        'field' => 'field_event_article',
        'value' => 1,
        'operator' => "=",
      ],
    ], $range, $elements);

    if ($nids) {
      $nodes = $nodesManager->loadMultiple($nids);
      if ($nodes) {
        foreach ($nodes as $node) {
          $uid = $node->getOwnerId();

          if ($uid == 0 && isset($node->revision_uid)) {
            $field = $node->get('revision_uid')->getValue();
            if ($field) {
              $uid = $field[0]['target_id'];
            }
          }

          $user = \Drupal\user\Entity\User::load($uid);

          $previews[$node->id()]['name'] = $node->title->value;
          $previews[$node->id()]['link'] = $this->getFodbaldEventAlias($node->id(), TRUE);
          $previews[$node->id()]['eventLink'] = $this->getFodbaldEventAlias($node->id());
          $previews[$node->id()]['description'] = $node->body->value;
          $previews[$node->id()]['description_short'] = mb_strimwidth($node->body->value, 0, 340, '...');
          $previews[$node->id()]['date'] = $node->field_event_date->value;
          $previews[$node->id()]['userId'] = $uid;
          $previews[$node->id()]['userName'] = $user->getUsername();
          $previews[$node->id()]['userLink'] = '/user/' . $uid;
          $previews[$node->id()]['userImage'] = '';
        }
      }
    }

    if ($slides && $previews) {
      $previews = array_chunk($previews, $slides);
    }

    return $previews;
  }

  public function getFodbaldTeamList() {

    $data = [];
    $teams = [];

    $database = \Drupal::database();

    if (!db_table_exists('taxonomy_term__field_weight')) {
      return [];
    }

    $query = $database->select('live_fodbold_team_list', 'tl');
    $query->join('taxonomy_term__field_weight', 'w', 'w.entity_id = tl.ltid');
    $query->join('taxonomy_term_field_data', 't', 't.tid = tl.ltid');

    $leagues = $query->fields('tl', ['ltid'])
      ->fields('t')
      ->distinct()
      ->execute()
      ->fetchAll();

    //->orderBy('w.field_weight_value')

    foreach ($leagues as $league) {

      $data[$league->ltid]['name'] = $league->name;
      $data[$league->ltid]['list'] = [];

      $query = $database->select('live_fodbold_team_list', 'tl');

      $query->join('taxonomy_term__field_weight', 'w', 'w.entity_id = tl.ttid');
      $query->join('taxonomy_term_field_data', 't', 't.tid = tl.ttid');
      $query->join('taxonomy_term__field_participant_logo', 'l', 'l.entity_id = tl.ttid');

      $teams = $query->fields('tl')
        ->fields('t')
        ->fields('l', ['field_participant_logo_target_id'])
        ->condition('tl.ltid', $league->ltid)
        ->orderBy('w.field_weight_value')
        ->execute()
        ->fetchAll();

      foreach ($teams as $team) {
        $data[$league->ltid]['list'][$team->ttid]['name'] = $team->name;
        $data[$league->ltid]['list'][$team->ttid]['link'] = $this->getTaxonomyAlias($team->ttid);
        $data[$league->ltid]['list'][$team->ttid]['img'] = $this->getImgUrl($team->field_participant_logo_target_id);
      }
    }

    return $data;
  }

  public function debug($responseObj) {
    \Drupal::logger('rp_cms_steve_integration_live_fodbald')
      ->warning('<pre><code>' . print_r($responseObj, TRUE) . '</code></pre>');
  }
}
