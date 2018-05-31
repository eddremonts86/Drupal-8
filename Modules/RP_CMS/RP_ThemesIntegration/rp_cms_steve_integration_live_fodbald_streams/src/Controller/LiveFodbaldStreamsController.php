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
	
	public function getSportTermID($parents = FALSE){
		$storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		$terms = $storage->loadByProperties(['name' => 'Football', 'vid' => 'sport']);
		
		if($terms){
			foreach($terms as $term){
				if($parents && $storage->loadParents($term->id())){
					return $term->id();
				}else if(!$parents && !$storage->loadParents($term->id())){
					return $term->id();
				}
			}	
		}
		
		return 0;
	}
	
	public function getFodbaldStreamsMenu(){
		$menu = [];
		$menu_name = 'fodbaldstreamsmenu';
		$menu_tree = \Drupal::menuTree();
		$manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		$parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);

		$tree = $menu_tree->load($menu_name, $parameters);
		$manipulators = [
			['callable' => 'menu.default_tree_manipulators:checkAccess'],
			['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
		];
		
		$tree = $menu_tree->transform($tree, $manipulators);

		foreach ($tree as $key => $item) {
			$menu[$key]['name'] = $item->link->getTitle();
			$menu[$key]['active'] = $item->inActiveTrail;
			$menu[$key]['link'] = $item->link->getUrlObject()->toString();
			if($item->hasChildren){
				foreach($item->subtree as $child){
					$menu[$key]['childs'][] = [
						'name' => $child->link->getTitle(),
						'link' => $child->link->getUrlObject()->toString(),
					];
				}
			}

			if($item->link->getPluginId() == 'rp_cms_steve_integration_live_fodbald_streams.leagues'){
				$leagues = $this->getLeaguesList();
				if($leagues){
					foreach($leagues as $league){
						$menu[$key]['childs'][] = [
							'name' => $league['name'],
							'link' => $league['link']
						];
					}
				}
			}

			if($item->link->getPluginId() == 'rp_cms_steve_integration_live_fodbald_streams.teams'){				
				$query = \Drupal::entityQuery('taxonomy_term');
				$query->condition('vid', "participant");
				$query->condition('field_participant_sport', $this->getSportTermID());
				$query->range(0, 20); 
				
				$terms = $manager->loadMultiple($query->execute());
				
				
				if($terms){
					foreach($terms as $term){
						$menu[$key]['childs'][] = [
							'name' => $term->name->value,
							'link' => $this->getTaxonomyAlias($term->id())
						];
					}
				}
			}
		}
		
		return $menu;
	}

	public function getFodbaldStreamsSchedule($mode = NULL, $days = 1, $dayLimit = NULL){
		$query = [];
		$data = [];
		$currentDay = strtotime("midnight", time());
		
		$nids = [];
		
		$nodesManager = \Drupal::entityTypeManager()->getStorage('node');
		$taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		
		switch($mode){
			case 'program':
				if($date = \Drupal::request()->query->get('date')){
					if($currentDay != $date){
						$data['controls']['prev'] = \Drupal\Core\Url::fromRoute('<current>', [], ['query' => ['date' => $date - 86400 * $days  > $currentDay ? $date - 86400 * $days : $currentDay]]);
					}
					$currentDay = $date;
				}

				$data['controls']['next'] = \Drupal\Core\Url::fromRoute('<current>', [], ['query' => ['date' => $currentDay + 86400 * $days]]);
				break;
			case 'home':
				for($day = 1; $day <= $days; $day++){
					$data['controls'][$currentDay + 86400 * ($day - 1)] = date('D, d M Y', $currentDay + 86400 * ($day - 1));
				}
				
				break;
		}
		
		for($day = 1; $day <= $days; $day++){
			$newQuery = $query;
			
			$newQuery[] = ['field' => 'field_event_date', 'value' => $currentDay + 86400 * ($day - 1), 'operator' => '>='];
			$newQuery[] = ['field' => 'field_event_date', 'value' => $currentDay + 86400 * $day - 1, 'operator' => '<='];
			
			$nids += $this->loadEvents($newQuery, $dayLimit);
		}
		
		$nodes = $nodesManager->loadMultiple($nids);
		
		foreach($nodes as $node){
			$eventDay = strtotime("midnight", $node->field_event_date->value);
	        $data['schedule'][$eventDay]['format'] = date('D, d M Y', $eventDay);
	        
			if(isset($node->field_event_tournament->target_id)){
				$league = $taxonomyManager->load($node->field_event_tournament->target_id);
				
				if(isset($league->field_logo->target_id)){
					$data['schedule'][$eventDay]['leagues'][$league->id()]['image'] = $this->getImgUrl($league->field_logo->target_id); 
				}
				
				$data['schedule'][$eventDay]['leagues'][$league->id()]['name'] = $league->name->value;
				$data['schedule'][$eventDay]['leagues'][$league->id()]['events'][$node->id()] = $this->getFormatEvent($node);
	        }       
		} 
		
		return $data;
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
	
	public function getFormatEvent($node, $providers = FALSE){
		$event = [];
		$taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		
    	$event['date'] = $node->field_event_date->value; 
		$event['name'] = $node->title->value;
		$event['link'] = $this->getNodeAlias($node->id());
		$event['description'] = $this->getShortcode($node->body->value); 
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
		
		if($providers){
			$event['providers'] = $this->getSeFodbaldProviders($node);
		}
		
		return $event;
	}
	
	public function loadEvents($params, $range = NULL, $pager = NULL){
		$query = \Drupal::entityQuery('node')
		    ->condition('status', 1)
	        ->condition('promote', 1)
	        ->condition('field_events_sport', $this->getSportTermID())
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
		
		$query = $database->select('fodbold_streams_team_list', 'tl')
			->fields('tl', ['id', 'ttid', 'ltid']);
			
		if($league){
			if($term = $this->getTaxonomyTermByUrl()){
				$query->condition('tl.ltid', $term->id(), '=');
			}
		}

		$result = $query->execute()->fetchAll();	
		
		foreach($result as $line){
			$taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
			if(!isset($data[$line->ltid])){
				$term = $taxonomyManager->load($line->ltid);
				
				if(!$term){
					continue;
				}

				$data[$line->ltid]['name'] = $term->name->value;
				$data[$line->ltid]['list'] = [];
			}
			
			if(!isset($data[$line->ltid]['list'][$line->ttid])){
				if(!isset($teams[$line->ttid])){
					$term = $taxonomyManager->load($line->ttid);
					
					if(!$term){
						continue;
					}
					
					$team[$line->ltid]['link'] = $this->getTaxonomyAlias($line->ttid);
					$team[$line->ltid]['name'] = $term->name->value;
					$team[$line->ltid]['img'] = $this->getImgUrl($term->field_participant_logo->target_id);
				}
				
				$data[$line->ltid]['list'][$line->ttid]['name'] = $team[$line->ltid]['name'];
				$data[$line->ltid]['list'][$line->ttid]['link'] = $team[$line->ltid]['link'];
				$data[$line->ltid]['list'][$line->ttid]['img'] = $team[$line->ltid]['img'];
			}	
		}
		return $data;
	}
	
	public function getLeaguesList(){
		$list = [];
		
	    $manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		$terms = $manager->loadTree('sport', $this->getSportTermID(TRUE), 1, TRUE);
			
		foreach($terms as $term){
			$leagues = $manager->loadTree('sport', $term->id(), 1, TRUE);
			
			if($leagues){
				foreach($leagues as $league){
					$list[$league->id()]['id'] = $league->id();
					$list[$league->id()]['name'] = $league->name->value;
					$list[$league->id()]['link'] = $this->getTaxonomyAlias($league->id());
					$list[$league->id()]['img'] = $this->getImgUrl($league->field_logo->target_id);
				}
			}
		}
		
		return $list;
	}
}
