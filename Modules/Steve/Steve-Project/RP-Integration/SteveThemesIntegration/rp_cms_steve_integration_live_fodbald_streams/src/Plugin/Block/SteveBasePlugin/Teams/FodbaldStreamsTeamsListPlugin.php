<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Teams;

use Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller\LiveFodbaldStreamsController;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ChangedCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Url;

/**
 * Provides a 'fodbaldstreamsteamslistplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsteamslistplugin",
 *  admin_label = @Translation("Fodbald Streams Teams List Plugin"),
 * )
 */
class FodbaldStreamsTeamsListPlugin extends BlockBase implements BlockPluginInterface{

	public function build() {
		$class = new LiveFodbaldStreamsController();
	
		$data =  [
			'#theme' => 'fodbaldstreamsteamslistplugin',
			'#tags' => $class->getTeamList(),
			'#background' => [],
			'#colors' => []
		];
		return $data;
	}
	

	public function blockForm($form, FormStateInterface $form_state) {
		$form = parent::blockForm($form, $form_state);
		$options = [];
		$list = [];
		$database = \Drupal::database();
		$manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
		$class = new LivefodbaldstreamsController();
		$config = $this->getConfiguration();
		
		$lists = $class->getLeaguesList();
	
		foreach($lists as $list){
			foreach($list['list'] as $league){
				$options[$list['name']][$league['id']] = $league['name'];
			}
		}
		
		$active = key($options[key($options)]);
		
		if($league = \Drupal::request()->query->get('league')){
			if($this->array_key_exists_r($league, $options)){
				$active = $league;
			}	
		}
	
		$form['select_league'] = [
			'#type' => 'select',
			'#title' => $this->t('Select League'),
			'#options' => $options,
			'#default_value' => $active,
			'#ajax' => [
				'callback' => 'Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Teams\FodbaldStreamsTeamsListPlugin::formRedirect',
				'wrapper' => 'AdminTeamListSelect',
				'event' => 'change',
				'progress' => [
				'type' => 'throbber',
				'message' => t('Redirecting...'),
				]
			]
		];
		
		$league = $manager->load($active);
		
		$form['select_teams'] = [
			'#type' => 'fieldset',
			'#title' => t('Select Teams - ') . $league->name->value,
	    	'#attributes' => [
				'id' => ['AdminTeamListSelect'],
			],			
		];
		
		$sport = $class->getSport(2, 'api');
		$query = \Drupal::entityQuery('taxonomy_term');
		$query->condition('vid', "participant");
		$query->condition('field_participant_sport', $sport['sportDrupalId']);
		$terms = $manager->loadMultiple($query->execute());	
		
		$records = $database->select('fodbold_streams_team_list', 'tl')
			->fields('tl', ['id', 'ttid', 'ltid'])
			->condition('ltid', $active, '=')
			->execute()
			->fetchAll();
				
		if($records){
			foreach($records as $record){
				$list[$record->ttid] = $record->ltid;
			}
		}
		
		foreach($terms as $term){			
			$form['select_teams'][$term->id()] = [
				'#type' => 'checkbox',
				'#title' => $term->name->value,
				'#value' => isset($list[$term->id()]) ? 1 : 0,
			];
		}
		
		return $form;
	}
	
	public function array_key_exists_r($key, $array){
		if(array_key_exists($key, $array)){ 
			return true;
		}

		foreach($array as $value){
			if(is_array($value)){
				if($this->array_key_exists_r($key, $value)){
					return true;
				}
			}
		}
		
		return false;
	}
 
	public function formRedirect(array &$form, FormStateInterface $form_state) : AjaxResponse {
		$values = $form_state->getUserInput();
		$response = new AjaxResponse();
		$element = [
			'#type' => 'item',
			'#title' => t('Redirecting...')
		];
		
		$renderer = \Drupal::service('renderer');
		$response->addCommand(new ReplaceCommand('#AdminTeamListSelect', $renderer->render($element)));
		$response->addCommand(new RedirectCommand(Url::fromRoute('<current>', ['league' => $values['settings']['select_league']])->toString()));
		
		return $response;
	}

 
	public function blockSubmit($form, FormStateInterface $form_state) {
	    parent::blockSubmit($form, $form_state);

	    $database = \Drupal::database();
	    $class = new LiveFodbaldStreamsController();
	    
	    $input  = $form_state->getUserInput();
	    $values = $form_state->getValue('select_teams');
	    
	    $records = $database->select('fodbold_streams_team_list', 'tl')
				->fields('tl', ['id', 'ttid', 'ltid'])
				->condition('ltid', $input['settings']['select_league'], '=')
				->execute()
				->fetchAll();
		
		if(isset($input['settings']['select_teams'])){
			foreach($input['settings']['select_teams'] as $id => $state){
				$unset = [];
				
				if($records){	
					foreach($records as $key => $record){
						if($record->ttid == $id){
							$unset[] = $key;
						}
					}
				}
				
				if($unset){
					foreach($unset as $key){
						unset($records[$key]);
					}
				}else{

					$database->insert('fodbold_streams_team_list')
						->fields([
						'ttid' => $id,
						'ltid' => $input['settings']['select_league'],
						])
						->execute();
				}
			}
			
			if($records){
				foreach($records as $record){
					$database->delete('fodbold_streams_team_list')
					->condition('id', $record->id)
					->execute();
				}
			}
		}
	}


  public function getCacheTags() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
    }
    else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }
}

