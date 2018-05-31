<?php
namespace Drupal\rp_cms_steve_integration_live_fodbald\Controller;

use Drupal\rp_client_base\Controller\SteveFrontendControler;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Component\Utility\Unicode;


/**
 * Class LiveFodbaldController.
 */

Class LiveFodbaldController extends SteveFrontendControler{

	public function fodbaldHomePage()
	{
		return array(
			'#theme' => 'fodbaldhomepage',
		);
	}
	public function fodbaldProgramPage()
	{
		return array(
			'#theme' => 'fodbaldprogrampage',
		);
	}
	public function fodbaldProvidersPage()
	{
		return array(
			'#theme' => 'fodbaldproviderspage',
		);
	}
	public function fodbaldLeaguesPage()
	{
		return array(
			'#theme' => 'fodbaldleaguespage',
		);
	}
	public function fodbaldTeamsPage()
	{
		return array(
			'#theme' => 'fodbaldteamspage',
		);
	}
	public function fodbaldPreviewsPage()
	{
		return array(
			'#theme' => 'fodbaldpreviewspage',
		);
	}
	public function fodbaldMatchPage()
	{
		return array(
			'#theme' => 'fodbaldmatchpage',
		);
	}
	public function fodbaldLeaguePage()
	{
		return array(
			'#theme' => 'fodbaldleaguepage',
		);
	}
	public function fodbaldTeamPage()
	{
		return array(
			'#theme' => 'fodbaldteampage',
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
	
	public function getFodbaldMenu(){
		$menu = [];
		$menu_name = 'fodbaldmenu';
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
		}
		
		return $menu;
	}
	
	public function generateEventArticleAlias($entity){
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

	    $context = array(
	      'module' => $entity->getEntityType()->getProvider(),
	      'op' => 'insert',
	      'source' => $source,
	      'data' => $data,
	      'bundle' => $entity->bundle(),
	      'language' => &$langcode,
	    );

	    $moduleHandler->alter('pathauto_pattern', $pattern, $context);
	
	    $alias = $token->replace($pattern->getPattern(), $data, array(
	      'clear' => TRUE,
	      'callback' => array($aliasCleaner, 'cleanTokenValues'),
	      'langcode' => $langcode,
	      'pathauto' => TRUE,
	    ), new BubbleableMetadata());
	
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

	public function getFodbaldEventAlias($id, $preview = NULL){
		$path = FALSE;
		$database = \Drupal::database();
		$nodesManager = \Drupal::entityTypeManager()->getStorage('node');
		$node = $nodesManager->load($id);
		
		if(!$node){
			return NULL;
		}
		
		$aliases = $database->select('url_alias', 'ua')
		    ->fields('ua', ['source', 'alias', 'pid'])
		    ->condition('source', "%" . $database->escapeLike('/node/1730') . "%", 'LIKE')
		    ->orderBy('pid', 'DESC')
		    ->execute()
		    ->fetchAll();
		    
		if(!$aliases){
			return '/node/' . $id;
		} 
			
		if($node->field_event_article->value && $preview){
			foreach($aliases as $alias){
				if(explode('/', $alias->alias)[1] == 'optakter'){
					$path = $alias->alias;
					break;
				}
			} 
			
			if($path){
				return $path;
			}
			
			$path = array_shift($aliases);
			
			return $path['alias'];
		}
		
		foreach($aliases as $alias){
			if(explode('/', $alias->alias)[1] != 'optakter'){
				$path = $alias->alias;
				break;
			}
		}
		
		if($path){
			return $path;
		}
		
		return '/node/' . $id;
	}
	
	public function formatedProviders(){
		$providers = $this->getStreamProviders();
		
		$formatted = [];
		
		foreach($providers as $provider){
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
			$formatted[] = $data;
		}
		
		return $formatted;
	}
	
	public function getFodbaldSchedule($mode = "page", $days = 5){
		$data = [];
		$scheduleList = [];
		$leagues = [];
		$participants = [];
		
		$currentDay = strtotime("midnight", time());
		$fromDate = $currentDay;
		
		$params = [];

		if($mode == "page"){
			if($date = \Drupal::request()->query->get('date')){
				$fromDate = strtotime($date);
			}
			
			$endDate = strtotime("tomorrow", $fromDate) - 1;
		
			for($i = -2; $i < 5; $i++){
				$timestamp = $fromDate + (86400 * $i);

				$data['pager']['days'][$timestamp]['active'] = FALSE; 
				$data['pager']['days'][$timestamp]['format'] = date("D", $timestamp) . '<br>' . date("d", $timestamp);
				$data['pager']['days'][$timestamp]['link'] = \Drupal\Core\Url::fromRoute('<current>', [], ['query' => ['date' => date("Y-m-d", $timestamp)]]);
				
				if($i == 0){
					$data['pager']['days'][$timestamp]['active'] = TRUE;
				}
			}

			$data['pager']['next'] = \Drupal\Core\Url::fromRoute('<current>', [], ['query' => ['date' => date("Y-m-d", $fromDate + 86400)]]);
			$data['pager']['prev'] = \Drupal\Core\Url::fromRoute('<current>', [], ['query' => ['date' => date("Y-m-d", $fromDate - 86400)]]);	
		}else if($mode == "block"){
			$endDate = $currentDay + (86400 * $days - 1);
		}else if($mode == 'league'){
			$endDate = $currentDay + (86400 * $days - 1);
			if($term = $this->getTaxonomyTermByUrl()){
				$params[] = ['field' => 'field_event_tournament', 'value' => $term->id(), 'operator' => '='];
			}
		}
		
		$params[] = ['field' => 'field_event_date', 'value' => $fromDate, 'operator' => '>='];
		$params[] = ['field' => 'field_event_date', 'value' => $endDate, 'operator' => '<='];
		
		$taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		$nodesManager = \Drupal::entityTypeManager()->getStorage('node');
		
		
        $nodes = $nodesManager->loadMultiple($this->loadMatch($params)); 
       
        foreach($nodes as $node){
	        $eventDay = strtotime("midnight", $node->field_event_date->value);
	        $scheduleList[$eventDay]['format'] = date('D, d M Y', $eventDay);
	        
	        if(isset($node->field_event_tournament->target_id)){
		        
		        if(!isset($leagues[$node->field_event_tournament->target_id])){
					$term = $taxonomyManager->load($node->field_event_tournament->target_id);
					$leagues[$node->field_event_tournament->target_id]['name'] = $term->name->value;
					
					if(isset($term->field_logo->target_id)){
						$leagues[$node->field_event_tournament->target_id]['image'] = $this->getImgUrl($term->field_logo->target_id); 
					}  
		        }
		        
		        if(isset($node->field_event_tournament->target_id)){
			        foreach($node->get('field_event_participants')->getValue() as $participant){
				        if(!isset($participants[$participant['target_id']])){
					    	$term = $taxonomyManager->load($participant['target_id']);
					    	$participants[$participant['target_id']]['name'] = $term->name->value;
					    	if(isset($term->field_participant_logo->target_id)){
						    	$participants[$participant['target_id']]['image'] = $this->getImgUrl($term->field_participant_logo->target_id);
					    	}	
				        }
				        $scheduleList[$eventDay]['leagues'][$node->field_event_tournament->target_id]['list'][$node->id()]['teams'][$participant['target_id']]['name'] = $participants[$participant['target_id']]['name'];
				        if(isset($participants[$participant['target_id']]['image'])){
				        	$scheduleList[$eventDay]['leagues'][$node->field_event_tournament->target_id]['list'][$node->id()]['teams'][$participant['target_id']]['image'] = $participants[$participant['target_id']]['image'];  
				        }
			        }
		        }
		        
		        $scheduleList[$eventDay]['leagues'][$node->field_event_tournament->target_id]['name'] = $leagues[$node->field_event_tournament->target_id]['name'];
		        
		        if($leagues[$node->field_event_tournament->target_id]['image']){
			        $scheduleList[$eventDay]['leagues'][$node->field_event_tournament->target_id]['image'] = $leagues[$node->field_event_tournament->target_id]['image'];
		        }
		        
		        $scheduleList[$eventDay]['leagues'][$node->field_event_tournament->target_id]['list'][$node->id()]['name'] = $node->title->value;
		        $scheduleList[$eventDay]['leagues'][$node->field_event_tournament->target_id]['list'][$node->id()]['date'] = date("H:i", $node->field_event_date->value);
		        $scheduleList[$eventDay]['leagues'][$node->field_event_tournament->target_id]['list'][$node->id()]['link'] = $this->getFodbaldEventAlias($node->id());  
	        }   
        }
        
        $data['schedule'] = $scheduleList;
        
		return $data;
	}
	
	
	public function loadMatch($params, $range = NULL, $pager = NULL){
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
	
	public function getFodbaldMatchData($loadBy = NULL, $quantity = 1, $loadProviders = FALSE){
		
		$nodes = [];
		$events = [];
		$now = time(); 
		$route = \Drupal::routeMatch()->getRouteName();
		$nodesManager = \Drupal::entityTypeManager()->getStorage('node');
		$taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
	
		switch($loadBy){
			case 'node':
				$nodes[] = $this->getNodeByUrl(1);
				break;
			case 'term':
				if($term = $this->getTaxonomyTermByUrl()){
					$params = [];
					if($term->getVocabularyId() == 'sport'){
						$params = [
							['field' => 'field_event_tournament', 'value' => $term->id(), 'operator' => "="],
							['field' => 'field_event_date', 'value' => $now, 'operator' => '>=']
						];
					}else if($term->getVocabularyId() == 'participant'){
						$params = [
							['field' => 'field_event_participants', 'value' => $term->id(), 'operator' => "="],
							['field' => 'field_event_date', 'value' => $now, 'operator' => '>=']
						];
					}
					
					if($params){
						$nodes = $nodesManager->loadMultiple($this->loadMatch($params, $quantity));
					}
				}
				break;
			default:
				$nodes = $nodesManager->loadMultiple($this->loadMatch([['field' => 'field_event_date', 'value' => $now, 'operator' => '>=']], $quantity));
				break;
		}
		
        if($nodes){
	        foreach($nodes as $node){		        
		        if($node instanceof \Drupal\node\NodeInterface){
			    	$events[$node->id()]['matchDate'] = $node->field_event_date->value; 
					$events[$node->id()]['matchName'] = $node->title->value;
					$events[$node->id()]['matchLink'] = $this->getFodbaldEventAlias($node->id());
					$events[$node->id()]['matchDescription'] = $this->getShortcode($node->body->value);       
			       			
					foreach($node->get('field_event_participants')->getValue() as $key => $team){
						$side = $key % 2 == 0 ? 'left' : 'right';
						
						$term = $taxonomyManager->load($team['target_id']);
						
						$events[$node->id()]['matchTeams'][$side]['name'] = $term->name->value;
						$events[$node->id()]['matchTeams'][$side]['description'] = $term->getDescription();
						$events[$node->id()]['matchTeams'][$side]['link'] = $this->getTaxonomyAlias($term->id());
						
						if(isset($term->field_participant_logo->target_id)){
							$events[$node->id()]['matchTeams'][$side]['image'] = $this->getImgUrl($term->field_participant_logo->target_id);
						}		
					}
					
					if($loadProviders){
						$streams = $this->getEventStream($node->id());
						if(!empty($streams['events']['Streamers'])){
							$events[$node->id()]['matchProviders'] = $streams['events']['Streamers'];
						}
					}
				}		       
	        }
        }
        
        return $events;	
	}
	
	public function getTeamPageInfo(){
		$data = [];
		$term = $this->getTaxonomyTermByUrl();
		$data['name'] = $term->name->value;
		$data['description'] = $term->getDescription();
		$data['link'] = $this->getTaxonomyAlias($term->id());
		if(isset($term->field_participant_logo->target_id)){
			$data['image'] = $this->getImgUrl($term->field_participant_logo->target_id);
		}	
		return $data;		
	}
	
	public function getLeaguePageInfo(){
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
	
	public function getFodbaldHomeTabs(){
		$data = [];
		$taxonomyManager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		
		$query = \Drupal::entityQuery('taxonomy_term');
	    $query->condition('vid', ["sport", "participant"], "IN");
	    
	    $or = $query->orConditionGroup();
		$or->condition('field_participant_front', 1);
		$or->condition('field_sport_front', 1);
		
		$query->condition($or);
		
	    $terms = $taxonomyManager->loadMultiple($query->execute());
			
		if($terms){
			foreach($terms as $term){
				$type = $term->getVocabularyId();
				$data[$type][$term->id()]['name'] = $term->name->value;
				$data[$type][$term->id()]['link'] = $this->getTaxonomyAlias($term->id());
				$data[$type][$term->id()]['frontTitle'] = $term->get('field_' . $type. '_front_title')->getValue(); 
				$data[$type][$term->id()]['frontContent'] = $term->get('field_' . $type. '_front_content')->getValue(); 
				$data[$type][$term->id()]['sponsorLink'] = $term->get('field_' . $type. '_sponsor_link')->getValue(); 			
			}
		}

		return $data;
	}
	
	public function getLeaguesList(){
		$list = [];
		
	    $manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		$terms = $manager->loadTree('sport', $this->getSportTermID(TRUE), 1, TRUE);
			
		foreach($terms as $term){
			$list[$term->id()]['name'] = $term->name->value;
			$list[$term->id()]['list'] = [];
			$leagues = $manager->loadTree('sport', $term->id(), 1, TRUE);
			
			if($leagues){
				foreach($leagues as $league){
					$list[$term->id()]['list'][$league->id()]['id'] = $league->id();
					$list[$term->id()]['list'][$league->id()]['name'] = $league->name->value;
					$list[$term->id()]['list'][$league->id()]['link'] = $this->getTaxonomyAlias($league->id());
					$list[$term->id()]['list'][$league->id()]['img'] = $this->getImgUrl($league->field_logo->target_id);
				}
			}
		}
		
		return $list;
	}
	
	public function getFodbaldPreview(){
		$data = [];
		$event = $this->getNodeByUrl(1);
		
		if($event){
			if($event->bundle() == 'events' && $event->field_event_article->value && explode('/', \Drupal::request()->getRequestUri())[1] == 'optakter'){
				$uid = $event->getOwnerId();
				
				if($uid == 0 && isset($event->revision_uid)){
					$field = $event->get('revision_uid')->getValue();
					if($field){
						$uid = $field[0]['target_id'];
					}	
				}
				
				$user = \Drupal\user\Entity\User::load($uid);
				
				$data['name'] = $event->title->name;
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
	
	public function getFodbaldPreviews($range = null, $elements = 10, $slides = null){
		$previews = [];
		$nodesManager = \Drupal::entityTypeManager()->getStorage('node');
		$nids = $this->loadMatch([['field' => 'field_event_article', 'value' => 1, 'operator' => "="]], $range, $elements);
		
		if($nids){
			$nodes = $nodesManager->loadMultiple($nids);
			if($nodes){
				foreach($nodes as $node){
					$uid = $node->getOwnerId();
					
					if($uid == 0 && isset($node->revision_uid)){
						$field = $node->get('revision_uid')->getValue();
						if($field){
							$uid = $field[0]['target_id'];
						}	
					}
					
					$user = \Drupal\user\Entity\User::load($uid);
					
					$previews[$node->id()]['name'] = $node->title->name;
					$previews[$node->id()]['link'] = $this->getFodbaldEventAlias($node->id(), TRUE);
					$previews[$node->id()]['eventLink'] = $this->getFodbaldEventAlias($node->id());
					$previews[$node->id()]['description'] = $node->body->value;
					$previews[$node->id()]['date'] = $node->field_event_date->value;
					$previews[$node->id()]['userId'] = $uid;
					$previews[$node->id()]['userName'] = $user->getUsername();
					$previews[$node->id()]['userLink'] = '/user/' . $uid;
					$previews[$node->id()]['userImage'] = '';
				}
			}
		}
		
		if($slides && $previews){
			$previews = array_chunk($previews, $slides);
		}
		
		return $previews;
	}
	
	public function getFodbaldTeamList(){
		$data = [];
		$teams = [];
		$database = \Drupal::database();
		$result = $database->select('live_fodbold_team_list', 'tl')
			->fields('tl', ['id', 'ttid', 'ltid'])
			->execute()
			->fetchAll();
			
		
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
	
	public function createTeamFields($id){
		$list = [];
		$database = \Drupal::database();
		$manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		
		$query = \Drupal::entityQuery('taxonomy_term');
		$query->condition('vid', "participant");
		$query->condition('field_participant_sport', $this->getSportTermID());
		
		$league = $manager->load($id);
		$terms = $manager->loadMultiple($query->execute());	
		
		$form = [
			'#type' => 'fieldset',
			'#title' => t('Select Teams - ') . $league->name->value,
	    	'#attributes' => [
				'id' => ['AdminTeamListSelect'],
			],			
		];
		
		$records = $database->select('live_fodbold_team_list', 'tl')
			->fields('tl', ['id', 'ttid', 'ltid'])
			->condition('ltid', $id, '=')
			->execute()
			->fetchAll();
				
		
		if($records){
			foreach($records as $record){
				$list[$record->ttid] = $record->ltid;
			}
		}
		
		foreach($terms as $term){			
			$form[$term->id()] = [
				'#type' => 'checkbox',
				'#title' => $term->name->value,
				'#value' => isset($list[$term->id()]) ? 1 : 0,
			];
		}

		return $form;
	}
	
	public function debug($responseObj){
		\Drupal::logger('rp_cms_steve_integration_live_fodbald')->warning('<pre><code>' . print_r($responseObj, TRUE) . '</code></pre>');
	}
}
