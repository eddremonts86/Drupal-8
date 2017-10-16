<?php

namespace Drupal\rp_sport;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Sport entities.
 *
 * @ingroup rp_sport
 */
class SportListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Sport ID');
    $header['name'] = $this->t('Name');
    $header['sport_info'] = $this->t('Sport info');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\rp_sport\Entity\Sport */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.sport.edit_form', [
          'sport' => $entity->id(),
        ]
      )
    );
    $sport_links_list = [
      '#theme' => 'item_list',
      '#items' => $this->buildSportInfoLinks($entity),
    ];
    $row['sport_info'] =  \Drupal::service('renderer')->render($sport_links_list);
    return $row + parent::buildRow($entity);
  }


  public function buildSportInfoLinks($entity) {
    $sport_links = [];
    $sports_info = $this->getSportInfo($entity);
    $sites_combos = $this->getSitesInfo();
    $sport_combos = $this->getSportCombos($sites_combos);
    $combos = $sport_combos['combos'];
    $combos_code = $sport_combos['combos_code'];

    // Build sport links
    foreach($combos_code as $i => $combo) {
      $sport_links[$i] = $this->l(
        $this->buildSportInfoLabelLink($combo[0],$combo[1],$combo[2],false),
        new Url(
          'entity.sport_info.add_form', [
            'edit[field_sport_info_sport]' => $entity->id(),
            'edit[field_sport_info_site]' => $combos[$i][0],
            'edit[field_sport_info_region]' => $combos[$i][1],
            'edit[field_sport_info_language]' => $combos[$i][2],
            'edit[name]' => $entity->label() .' '. $this->buildSportInfoLabelLink($combo[0], $combo[1],$combo[2],false, false),
            'destination' => '/admin/rp/entity-content/sport'
          ]
        )
      );
    }

    // Get sport infos already created
    $sports_info_combos = [];
    if(!empty($sports_info)) {
      foreach($sports_info as $id => $sport_info){
        $sports_info_combos[$sport_info->id()] = [$sport_info->get('field_sport_info_site')->entity->id(), $sport_info->get('field_sport_info_region')->entity->id(), $sport_info->get('field_sport_info_language')->entity->id()];
      }
    }

    // Replace exists sport info links
    if(!empty($sports_info_combos) && !empty($combos)) {
      foreach($sports_info_combos as $sic_id => $sic){
        foreach($combos as $i => $combo){
          if($sic == $combo)
            $sport_links[$i] = $this->l(
              $this->buildSportInfoLabelLink($combos_code[$i][0], $combos_code[$i][1],$combos_code[$i][2]),
              new Url(
                'entity.sport_info.edit_form', [
                  'sport_info' => $sic_id,
                  'destination' => '/admin/rp/entity-content/sport'
                ]
              )
            );
        }
      }
    }
    return $sport_links;

  }

  public function getSportInfo($entity) {
    $query = \Drupal::service('entity.query')->get('sport_info');

    $query->condition('field_sport_info_sport', $entity->id());

    $ids = $query->execute();

    if (empty($ids)) {
      return [];
    }
    else {
      $storage = \Drupal::entityManager()->getStorage('sport_info');
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

  public function getSportCombos($sites_info) {
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

  public function buildSportInfoLabelLink($site,$region,$language,$edit = true, $label_print = true) {
    $label = '';
    if($label_print) {
      $label = ($edit) ? t('Edit') : t('Create');
      $label .= ': ';
    }
    return $label . t('Site @site / Region @region / Language @language',['@site' =>$site, '@region'=>$region,'@language'=>$language]);
  }
}
