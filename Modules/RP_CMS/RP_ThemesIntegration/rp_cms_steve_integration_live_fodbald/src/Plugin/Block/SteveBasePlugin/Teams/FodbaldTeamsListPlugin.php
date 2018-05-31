<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Teams;

use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;
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
 * Provides a 'fodbaldteamslistplugin' block.
 *
 * @Block(
 *  id = "fodbaldteamslistplugin",
 *  admin_label = @Translation("Fodbald Teams List Plugin"),
 * )
 */
class FodbaldTeamsListPlugin extends BlockBase implements BlockPluginInterface{

	public function build() {
		$class = new LiveFodbaldController();
	
		$data =  [
			'#theme' => 'fodbaldteamslistplugin',
			'#tags' => $class->getFodbaldTeamList(),
			'#background' => [],
			'#colors' => []
		];
		return $data;
	}
	
	public function blockForm($form, FormStateInterface $form_state) {
		$form = parent::blockForm($form, $form_state);
		$options = [];
		$class = new LiveFodbaldController();
		$config = $this->getConfiguration();
		
		$lists = $class->getLeaguesList();
	
		foreach($lists as $list){
			foreach($list['list'] as $league){
				$options[$league['id']] = $league['name'];
			}
		}
		
		$active = key($options);
		
		if($league = \Drupal::request()->query->get('league')){
			if(isset($options[$league])){
				$active = $league;
			}	
		}
	
		$form['select_league'] = [
			'#type' => 'select',
			'#title' => $this->t('Select League'),
			'#options' => $options,
			'#default_value' => $active,
			'#ajax' => [
				'callback' => 'Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Teams\FodbaldTeamsListPlugin::formRedirect',
				'wrapper' => 'AdminTeamListSelect',
				'event' => 'change',
				'progress' => [
				'type' => 'throbber',
				'message' => t('Redirecting...'),
				]
			]
		];
		
		$form['select_teams'] = $class->createTeamFields($active);
		
		return $form;
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
	    $class = new LiveFodbaldController();
	    
	    $input  = $form_state->getUserInput();
	    $values = $form_state->getValue('select_teams');
	    
	    $records = $database->select('live_fodbold_team_list', 'tl')
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

					$database->insert('live_fodbold_team_list')
						->fields([
						'ttid' => $id,
						'ltid' => $input['settings']['select_league'],
						])
						->execute();
				}
			}
			
			if($records){
				foreach($records as $record){
					$database->delete('live_fodbold_team_list')
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

