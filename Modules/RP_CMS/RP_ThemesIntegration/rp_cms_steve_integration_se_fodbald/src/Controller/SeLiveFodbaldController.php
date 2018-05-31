<?php
namespace Drupal\rp_cms_steve_integration_se_fodbald\Controller;

use Drupal\Core\Link;
use Drupal\rp_client_base\Controller\SteveFrontendControler;

/**
 * Class SeLiveFodbaldController.
 */

Class SeLiveFodbaldController extends SteveFrontendControler{

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
	
	public function getSeFodbaldSchedule($mode = NULL, $days = 1){
		$query = [];
		$schedule = [];
		$currentDay = strtotime("midnight", time());
		$nodesManager = \Drupal::entityTypeManager()->getStorage('node');
		$taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		
		switch($mode){
			case 'node':
				$event = $this->getNodeByUrl(1);
				if(isset($event->field_event_tournament->target_id)){
					$query[] = ['field' => 'field_event_tournament', 'value' => $event->field_event_tournament->target_id, 'operator' => '='];
				}			
				break;
			case 'term':
				if($term = $this->getTaxonomyTermByUrl()){
					$query[] = ['field' => 'field_event_tournament', 'value' => $term->id(), 'operator' => '='];
				}
				break;
		}
			
		$endDay = $currentDay + (86400 * $days - 1);
		
		$query[] = ['field' => 'field_event_date', 'value' => $currentDay, 'operator' => '>='];
		$query[] = ['field' => 'field_event_date', 'value' => $endDay, 'operator' => '<='];
		
		$nodes = $nodesManager->loadMultiple($this->loadEvents($query));
		
		foreach($nodes as $node){
			$eventDay = strtotime("midnight", $node->field_event_date->value);
	        $schedule[$eventDay]['format'] = date('D, d M Y', $eventDay);
	        
			if(isset($node->field_event_tournament->target_id)){
				$league = $taxonomyManager->load($node->field_event_tournament->target_id);
				
				if(isset($league->field_logo->target_id)){
					$schedule[$eventDay]['leagues'][$league->id()]['image'] = $this->getImgUrl($league->field_logo->target_id); 
				}
				
				$schedule[$eventDay]['leagues'][$league->id()]['name'] = $league->name->value;
				$schedule[$eventDay]['leagues'][$league->id()]['events'][$node->id()] = $this->getFormatEvent($node);
	        }       
		} 
		
		return $schedule;
	}
	
	public function getSeFodbaldMenu(){
		$menu = [];
		$menu_name = 'sefodbaldmenu';
		$menu_tree = \Drupal::menuTree();
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

			if($item->link->getPluginId() == 'rp_cms_steve_integration_se_fodbald.league'){
				$leagues = $this->getLeagueList(15);
				if($leagues){
					foreach($leagues as $league){
						$menu[$key]['childs'][] = [
							'name' => $league['name'],
							'link' => $league['link']
						];
					}
				}
			}
		}
		
		return $menu;
	}
	
	public function getSeFodbaldBreadcrumb(){
		$data = [];
		$links = \Drupal::service('breadcrumb')->build(\Drupal::routeMatch())->getLinks();
		
		if($links){
			$elements = explode('.', implode('.d.', array_keys($links)));
			
			foreach($elements as $element){
				if($element !== 'd' && isset($links[$element])){
					$text = $links[$element]->getText();
					
					if(!is_string($text)){
						$text = $text->render();
					}
					
					$data[] = [
						'text' => $text,
						'type' => 'link',
						'url' => $links[$element]->getUrl()->toString() 
					];
				}else if($element === 'd'){
					$data[] = [
						'type' => 'delimiter'
					];
				}
			}
		}
		
		return $data;	
	}
	
	public function getSeFodbaldLeagueInfo(){
		$data = [];
		$term = $this->getTaxonomyTermByUrl();
		$data['name'] = $term->name->value;
		$data['description'] = $term->getDescription();
		$data['link'] = $this->getTaxonomyAlias($term->id());
		if(isset($term->field_logo->target_id)){
			$data['image'] = $this->getImgUrl($term->field_logo->target_id);
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

	public function getEvents($source = null, $range = 3, $loadProviders = false){
		$data = [];
		$now = time();
		$nodes = [];
		$nodesManager = \Drupal::entityTypeManager()->getStorage('node');

		switch($source){
			case 'node':
				$nodes[] = $this->getNodeByUrl(1);
				break;
			case 'term':
				if($term = $this->getTaxonomyTermByUrl()){	
					$params = [
						['field' => 'field_event_tournament', 'value' => $term->id(), 'operator' => "="],
						['field' => 'field_event_date', 'value' => $now, 'operator' => '>=']
					];
					
					$nodes = $nodesManager->loadMultiple($this->loadEvents($params, $range));
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

	public function getLeagueList($limit = 0){
		$list = [];
		
	    $manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		$terms = $manager->loadTree('sport', $this->getSportTermID(TRUE), 1, TRUE);
			
		foreach($terms as $term){
			$leagues = $manager->loadTree('sport', $term->id(), 1, TRUE);
			if($leagues){
				foreach($leagues as $league){
					
					if($limit && count($list) == $limit){
						break;	
					}
					
					$list[$league->id()]['id'] = $league->id();
					$list[$league->id()]['name'] = $league->name->value;
					$list[$league->id()]['link'] = $this->getTaxonomyAlias($league->id());
					$list[$league->id()]['img'] = $this->getImgUrl($league->field_logo->target_id);
				}
			}
		}
		
		return $list;
	}
	
	public function getSeFodbaldProviders($node = null){
		$data = [];
		$providers = [];
		$taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		
		if($node){
			$tids = [];
			$region = $_SESSION["region"];
			
			if (isset($node->field_events_properties->value)) {
				$eventProperties = \GuzzleHttp\json_decode($node->field_events_properties->value);
			}			
			
		    foreach ($eventProperties->$region->streamers as $steam) {
				foreach ($steam->meta as $metas) {
					if ($metas->channel == $_SESSION["channel"]) {
						$tids[] = $steam->id;
					}
				}
		    }
		    
		    if($tids){ 
		    	$providers = $taxonomyManager->loadMultiple($tids);
		    }else{
			    $providers = $this->getStreamList();
		    }
		}else{
			$providers = $this->getStreamProviders();
		}

		if($providers){
			foreach($providers as $provider){
				$data[$provider->id()]['id'] = $provider->id();			
				$data[$provider->id()]['name'] = $provider->name->value;
				$data[$provider->id()]['sponsored'] = $provider->field_stream_provider_sponsor->value;
				$data[$provider->id()]['link'] = '/';
				$data[$provider->id()]['review'] = 'bet365 viser kampe fra bl.a. La Liga, Serie A, Bundesligaen & mange flere…';
				$data[$provider->id()]['quality'] = 'Billede og lyd er i top hos bet365. Kvaliteten sikrer en god fodboldoplevelse.';
				$data[$provider->id()]['price'] = 'Du skal have penge på kontoen for at kunne livestreame';
				$data[$provider->id()]['image'] = $this->getImgUrl($provider->field_streamprovider_logo->target_id);
			}	
		}
		
		return $data;
	}
	
	public function debug($responseObj){
		\Drupal::logger('rp_cms_steve_integration_se_fodbald')->warning('<pre><code>' . print_r($responseObj, TRUE) . '</code></pre>');
	}	
}
