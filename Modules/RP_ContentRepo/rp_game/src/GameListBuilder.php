<?php

namespace Drupal\rp_game;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Game entities.
 *
 * @ingroup rp_game
 */
class GameListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Game ID');
    $header['name'] = $this->t('Name');
    $header['game_info'] = $this->t('Game info');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\rp_game\Entity\Game */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.game.edit_form', [
          'game' => $entity->id(),
        ]
      )
    );
    $game_links_list = [
      '#theme' => 'item_list',
      '#items' => $this->buildGameInfoLinks($entity),
    ];
    $row['game_info'] =  \Drupal::service('renderer')->render($game_links_list);
    return $row + parent::buildRow($entity);
  }


  public function buildGameInfoLinks($entity) {
    $game_links = [];
    $games_info = $this->getGameInfo($entity);
    $sites_combos = $this->getSitesInfo();
    $game_combos = $this->getGameCombos($sites_combos);
    $combos = $game_combos['combos'];
    $combos_code = $game_combos['combos_code'];

    // Build game links
    foreach($combos_code as $i => $combo) {
      $game_links[$i] = $this->l(
        $this->buildGameInfoLabelLink($combo[0],$combo[1],$combo[2],false),
        new Url(
          'entity.game_info.add_form', [
            'edit[field_game_info_game]' => $entity->id(),
            'edit[field_game_info_site]' => $combos[$i][0],
            'edit[field_game_info_region]' => $combos[$i][1],
            'edit[field_game_info_language]' => $combos[$i][2],
            'edit[name]' => $entity->label() .' '. $this->buildGameInfoLabelLink($combo[0], $combo[1],$combo[2],false, false),
            'destination' => '/admin/rp/entity-content/game'
          ]
        )
      );
    }

    // Get game infos already created
    $games_info_combos = [];
    if(!empty($games_info)) {
      foreach($games_info as $id => $game_info){
        $games_info_combos[$game_info->id()] = [$game_info->get('field_game_info_site')->entity->id(), $game_info->get('field_game_info_region')->entity->id(), $game_info->get('field_game_info_language')->entity->id()];
      }
    }

    // Replace exists game info links
    if(!empty($games_info_combos) && !empty($combos)) {
      foreach($games_info_combos as $sic_id => $sic){
        foreach($combos as $i => $combo){
          if($sic == $combo)
            $game_links[$i] = $this->l(
              $this->buildGameInfoLabelLink($combos_code[$i][0], $combos_code[$i][1],$combos_code[$i][2]),
              new Url(
                'entity.game_info.edit_form', [
                  'game_info' => $sic_id,
                  'destination' => '/admin/rp/entity-content/game'
                ]
              )
            );
        }
      }
    }
    return $game_links;

  }

  public function getGameInfo($entity) {
    $query = \Drupal::service('entity.query')->get('game_info');

    $query->condition('field_game_info_game', $entity->id());

    $ids = $query->execute();

    if (empty($ids)) {
      return [];
    }
    else {
      $storage = \Drupal::entityManager()->getStorage('game_info');
      return $storage->loadMultiple($ids);
    }

  }

  public function getSitesInfo() {
    $entity_name = 'site';
    $ids = \Drupal::entityQuery($entity_name)->execute();
    $storage_handler = \Drupal::entityTypeManager()->getStorage($entity_name);
    $entities = $storage_handler->loadMultiple($ids);

    $sites = [];
    foreach($entities as $id => $entity) {
      foreach ($entity->get('field_site_regions') as $item_r) {
        if ($item_r->entity) {
          $regions[] = $item_r->entity->id();
          $regions_code[] = $item_r->entity->get('field_region_code')->value;
        }
      }
      foreach ($entity->get('field_site_languages') as $item_l) {
        if ($item_l->entity) {
          $languages[] = $item_l->entity->id();
          $languages_code[] = $item_l->entity->get('field_language_content_code')->value;
        }
      }
      $sites[$id] = [
        'name' => $entity->getName(),
        'regions' => $regions,
        'regions_code' => $regions_code,
        'languages' => $languages,
        'languages_code' => $languages_code
      ];
    }

    return $sites;

  }

  public function getGameCombos($sites_info) {
    $combos = []; $combos_code = [];

    foreach($sites_info as $site_id => $site_info) {
      foreach($site_info['regions'] as $i => $region_id) {
        foreach($site_info['languages'] as $j => $language_id) {
          $combos[] = [$site_id, $region_id, $language_id];
          $combos_code[] = [$site_info['name'], $site_info['regions_code'][$i],$site_info['languages_code'][$j]];
        }
      }
    }
    return ['combos' => $combos, 'combos_code' => $combos_code];
  }

  public function buildGameInfoLabelLink($site,$region,$language,$edit = true, $label_print = true) {
    $label = '';
    if($label_print) {
      $label = ($edit) ? t('Edit') : t('Create');
      $label .= ': ';
    }
    return $label . t('Site @site / Region @region / Language @language',['@site' =>$site, '@region'=>$region,'@language'=>$language]);
  }
}
