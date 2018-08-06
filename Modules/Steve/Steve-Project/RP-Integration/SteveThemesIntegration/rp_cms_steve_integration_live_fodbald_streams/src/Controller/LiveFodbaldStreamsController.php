<?php
namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller;

use Drupal\rp_client_base\Controller\SteveFrontendControler;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Component\Utility\Unicode;


/**
 * Class LiveFodbaldController.
 */

Class LiveFodbaldStreamsController extends SteveFrontendControler{


	public function fodbaldStreamsProgramPage()
	{
		return array(
			'#theme' => 'fodbaldstreamsprogrampage',
		);
	}
	public function fodbaldStreamsLeaguesPage()
	{
		return array(
			'#theme' => 'fodbaldstreamsleaguespage',
		);
	}
	public function fodbaldStreamsTeamsPage()
	{
		return array(
			'#theme' => 'fodbaldstreamsteamspage',
		);
	}

	public function FodbaldStreamsMenuTeamsLink(&$menu_item){
		$manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		$query = \Drupal::database()
			->select('taxonomy_term_field_data', 't');
			$query->join('taxonomy_term_hierarchy', 'h', 'h.tid = t.tid');
			$query->join('taxonomy_term__field_weight', 'w', 'w.entity_id = t.tid');
		$terms = $query->fields('t', ['tid'])
	        ->condition('t.vid', 'participant')
	        ->condition('h.parent', 0)
	        ->condition('t.default_langcode', 1)
	        ->orderBy('w.field_weight_value')
	        ->range(0, 20)
	        ->execute()
	        ->fetchAll();

		if($terms){
			foreach($terms as $term){
				$term = $manager->load($term->tid);
				$menu_item['childs'][] = [
					'name' => $term->name->value,
					'link' => $this->getTaxonomyAlias($term->id())
				];
			}
		}
	}
	
	public function FodbaldStreamsMenuLeaguesLink(&$menu_item){
		$lists = $this->getLeaguesList();
		if($lists){
			foreach($lists as $list){
				foreach($list['list'] as $league){
					$menu_item['childs'][] = [
						'name' => $league['name'],
						'link' => $league['link']
					];
				}
			}
		}
	}
	
	public function FodbaldStreamsScheduleFormatModificator(&$data, $node){
		$data['FodbaldStreamsPreview'] = $node['field_fodbold_streams_preview'][0]['value'];   
	}
	
	public function getFodbaldStreamsSchedulePage($format = "Y-m-d"){
		$currDay = strtotime("midnight", time());
		
		$time = $currDay;
		$schedule = NULL;
		
		if($get = \Drupal::request()->query->get('date')){
			$time = strtotime("midnight", strtotime($get));
		}
		
		$schedule = $this->getSchedulePlusTree(0, $format, 5, 0, $this->getSport(2, 'api'), $time, NULL, ['FodbaldStreamsScheduleFormatModificator']);
		
		if($time != $currDay && $time > $currDay){
			$schedule['prev'] = \Drupal\Core\Url::fromRoute('<current>', [], ['query' => ['date' => date($format, $time - 86400 * 5  > $currDay ? $time - 86400 * 5 : $currDay)]]);
		}
			
		$schedule['next'] = \Drupal\Core\Url::fromRoute('<current>', [], ['query' => ['date' => date($format, $time + 86400 * 5)]]);
			
		return $schedule;
	}
	
	public function getTermInfo(){
		$data = [];
		$term = $this->getTaxonomyTermByUrl();
		$data['name'] = $term->name->value;
		$data['description'] = $term->getDescription();
		$data['link'] = $this->getTaxonomyAlias($term->id());
		
		if($term->getVocabularyId() == 'sport'){
			if(isset($term->field_logo->target_id)){
				$data['image'] = $this->getImgUrl($term->field_logo->target_id);
			}
		}else if($term->getVocabularyId() == 'participant'){
			if(isset($term->field_participant_logo->target_id)){
				$data['image'] = $this->getImgUrl($term->field_participant_logo->target_id);
			}
		}

		return $data;
	}
	
	public function getFormatEvent($node){
		$event = [];
		$taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		
		$uid = $node->getOwnerId();
		
		if($uid == 0 && isset($node->revision_uid)){
			$field = $node->get('revision_uid')->getValue();
			if($field){
				$uid = $field[0]['target_id'];
			}	
		}
		
		$user = \Drupal\user\Entity\User::load($uid);
		
    	$event['date'] = $node->field_event_date->value; 
		$event['name'] = $node->title->value;
		$event['link'] = $this->getNodeAlias($node->id());
		$event['description'] = $this->getShortcode($node->body->value);
		$event['user'] = $user->getUsername();
		$event['userLink'] = '/user/' . $uid;
		$event['preview'] = $node->field_fodbold_streams_preview->value;      
       			
		foreach($node->get('field_event_participants')->getValue() as $key => $team){
			$side = $key % 2 == 0 ? 'left' : 'right';
			
			$term = $taxonomyManager->load($team['target_id']);
			
			$event['teams'][$side]['name'] = $term->name->value;
			$event['teams'][$side]['description'] = $term->getDescription();
			$event['teams'][$side]['link'] = $this->getTaxonomyAlias($term->id());
			
			if(isset($term->field_participant_logo->target_id)){
				$event['teams'][$side]['image'] = $this->getImgUrl($term->field_participant_logo->target_id);
			}		
		}

		return $event;
	}
	
	public function loadEvents($params, $range = NULL, $pager = NULL){
		$sport = $this->getSport(2, 'api');
		
		$query = \Drupal::entityQuery('node')
		    ->condition('status', 1)
	        ->condition('promote', 1)
	        ->condition('field_events_sport', $sport['sportDrupalId'])
	        ->condition('type', 'events');
	        
	    foreach($params as $condition){
		   $query->condition($condition['field'], $condition['value'], $condition['operator']); 
	    }    
	         
	    if($range){
			$query->range(0, $range); 
	    }
	     
		$query->sort('field_event_date', 'ASC');
		$query->sort('field_event_tournament', 'ASC');
		
		if($pager){
			$query->pager($pager);
		}
	       
	    return $query->execute();
	}

	public function getEvents($source = null, $range = 3, $loadProviders = false, $old = FALSE){
		$data = [];
		$now = time();
		$nodes = [];
		$db = \Drupal::database();
		$nodesManager = \Drupal::entityTypeManager()->getStorage('node');

		switch($source){
			case 'node':
				$nodes[] = $this->getNodeByUrl(1);
				break;
			case 'term':
				if($term = $this->getTaxonomyTermByUrl()){		
					$nids = [];

					if($term->getVocabularyId() == 'sport'){
						$params['league'] = ['field' => 'field_event_tournament', 'value' => $term->id(), 'operator' => "="];
						$params['date'] = ['field' => 'field_event_date', 'value' => $now, 'operator' => '>='];
					}else if($term->getVocabularyId() == 'participant'){
						$params['team'] = ['field' => 'field_event_participants', 'value' => $term->id(), 'operator' => "="];
						$params['date'] = ['field' => 'field_event_date', 'value' => $now, 'operator' => '>='];
					}			
					
					$nids = $this->loadEvents($params, $range);
					
					if(!$nids && $old){
						$params['date']['operator'] = '<=';
						$nids = $this->loadEvents($params, $range);
					}
								
					$nodes = $nodesManager->loadMultiple($nids);
				}
				break;
			case 'league':
				if($node = $this->getNodeByUrl(1)){
					if(isset($node->field_event_tournament->target_id)){
						$params = [
							['field' => 'field_event_tournament', 'value' => $node->field_event_tournament->target_id, 'operator' => "="],
							['field' => 'field_event_date', 'value' => $now, 'operator' => '>=']
						];
						$nodes = $nodesManager->loadMultiple($this->loadEvents($params, $range));
					}
				}
				break;
			case 'team':
				if($term = $this->getTaxonomyTermByUrl()){
					$params = [
						['field' => 'field_event_participants', 'value' => $term->id(), 'operator' => "="],
						['field' => 'field_event_date', 'value' => $now, 'operator' => '<=']
					];
				}
				$nodes = $nodesManager->loadMultiple($this->loadEvents($params, $range));
				break;
			case 'eventbyteam':
				if($node = $this->getNodeByUrl(1)){
					$teams = $node->get('field_event_participants')->getValue(); 
					$params = [
						['field' => 'field_event_participants', 'value' => $teams[0]['target_id'], 'operator' => "="],
						['field' => 'field_event_date', 'value' => $now, 'operator' => '<=']
					];
				}
				
				$nodes = $nodesManager->loadMultiple($this->loadEvents($params, $range));
				break;
			case 'previews':
			
				$params = [
					['field' => 'field_event_date', 'value' => strtotime("midnight", $now), 'operator' => '>='],
					['field' => 'field_event_date', 'value' => strtotime("midnight", $now) + 86399, 'operator' => '<='],
					['field' => 'field_fodbold_streams_preview', 'value' => 1, 'operator' => '=']
				];
			
				$nodes = $nodesManager->loadMultiple($this->loadEvents($params, $range));
				break;
			default:
				$nodes = $nodesManager->loadMultiple($this->loadEvents([['field' => 'field_event_date', 'value' => $now, 'operator' => '>=']], $range));
				break;	
		}
		
		if($nodes){
	        foreach($nodes as $node){		        
		        if($node instanceof \Drupal\node\NodeInterface){
			        $data[$node->id()] = $this->getFormatEvent($node, $loadProviders);
				}		       
	        }
        }
        
		return $data;
	}
	
	public function debug($responseObj){
		\Drupal::logger('rp_cms_steve_integration_live_fodbald_streams')->warning('<pre><code>' . print_r($responseObj, TRUE) . '</code></pre>');
	}
	
	public function getTeamList($league = FALSE){
		$data = [];
		$teams = [];
		
		$database = \Drupal::database();
		
		if(!db_table_exists('taxonomy_term__field_weight')){
			return [];
		}
		
		$query = $database->select('fodbold_streams_team_list', 'tl');
		
		$query->join('taxonomy_term__field_weight', 'w', 'w.entity_id = tl.ltid');
		$query->join('taxonomy_term_field_data', 't', 't.tid = tl.ltid');
		
		if($league){
			if($term = $this->getTaxonomyTermByUrl()){
				$query->condition('tl.ltid', $term->id(), '=');
			}
		}
		
		$leagues = $query->fields('tl', ['ltid'])->fields('t')->distinct()->execute()->fetchAll();
//->orderBy('stevecms_taxonomy_term__field_weight.field_weight_value')
		foreach($leagues as $league){
			
			$data[$league->ltid]['name'] = $league->name;
			$data[$league->ltid]['list'] = [];
			
			$query = $database->select('fodbold_streams_team_list', 'tl');
			
			$query->join('taxonomy_term__field_weight', 'w', 'w.entity_id = tl.ttid');
			$query->join('taxonomy_term_field_data', 't', 't.tid = tl.ttid');
			$query->join('taxonomy_term__field_participant_logo', 'l', 'l.entity_id = tl.ttid');
			
			$teams = $query->fields('tl')->fields('t')->fields('l', ['field_participant_logo_target_id'])->condition('tl.ltid', $league->ltid)->orderBy('w.field_weight_value')->execute()->fetchAll();
			
			foreach($teams as $team){
				$data[$league->ltid]['list'][$team->ttid]['name'] = $team->name;
				$data[$league->ltid]['list'][$team->ttid]['link'] = $this->getTaxonomyAlias($team->ttid);
				$data[$league->ltid]['list'][$team->ttid]['img'] = $this->getImgUrl($team->field_participant_logo_target_id);
			}
		}
		
		return $data;	
	}
	
	public function sortTaxonomyTreeByWeight($vid, $parent = 0){
		
		if(!db_table_exists('taxonomy_term__field_weight')){
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
	
	public function getLeaguesList($limit = 0){
		$list = [];
		
		$sport = $this->getSport(2, 'api');
	    $manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
	    $parent = $manager->loadTree('sport', $sport['sportDrupalId'], 1, FALSE);
	    
	    $parent = reset($parent);
	    
	    $terms = $this->sortTaxonomyTreeByWeight('sport', $parent->tid);
			
		foreach($terms as $term){
			$term = $manager->load($term->tid);
			
			$list[$term->id()]['name'] = $term->name->value;
			$list[$term->id()]['list'] = [];
			$leagues = $this->sortTaxonomyTreeByWeight('sport', $term->id());
			
			if($leagues){
				foreach($leagues as $league){
					
					if($limit && count($list) == $limit){
						break;	
					}
					
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
}
