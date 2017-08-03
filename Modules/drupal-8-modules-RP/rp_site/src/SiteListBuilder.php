<?php

namespace Drupal\rp_site;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Site entities.
 *
 * @ingroup rp_site
 */
class SiteListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Site ID');
    $header['name'] = $this->t('Name');
    $header['site_info'] = $this->t('Site info');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\rp_site\Entity\Site */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.site.edit_form', [
          'site' => $entity->id(),
        ]
      )
    );
    $site_links_list = [
        '#theme' => 'item_list',
        '#items' => $this->buildSiteInfoLinks($entity),
    ];
    $row['site_info'] =  \Drupal::service('renderer')->render($site_links_list);

    return $row + parent::buildRow($entity);
  }

  public function buildSiteInfoLinks($entity) {
    $site_links = [];
    $sites_info = $this->getSiteInfo($entity);
    $site_combos = $this->getSiteCombos($entity);
    $combos = $site_combos['combos'];
    $combos_code = $site_combos['combos_code'];

    // Build site links
    foreach($combos_code as $i => $combo) {
      $site_links[$i] = $this->l(
          $this->buildSiteInfoLabelLink($combo[0],$combo[1],false),
          new Url(
              'entity.site_info.add_form', [
                  'edit[field_site_info_site]' => $entity->id(),
                  'edit[field_site_info_region]' => $combos[$i][0],
                  'edit[field_site_info_language]' => $combos[$i][1],
                  'edit[name]' => $entity->label() .' '. $this->buildSiteInfoLabelLink($combo[0],$combo[1],false, false),
                  'destination' => '/admin/rp/entity-content/site'
              ]
          )
      );
    }

    // Get site infos already created
    $sites_info_combos = [];
    if(!empty($sites_info)) {
      foreach($sites_info as $id => $site_info){
        $sites_info_combos[$site_info->id()] = [$site_info->get('field_site_info_region')->entity->id(), $site_info->get('field_site_info_language')->entity->id()];
      }
    }

    // Replace exists site info links
    if(!empty($sites_info_combos) && !empty($combos)) {
      foreach($sites_info_combos as $sic_id => $sic){
        foreach($combos as $i => $combo){
          if($sic == $combo)
            $site_links[$i] = $this->l(
                $this->buildSiteInfoLabelLink($combos_code[$i][0],$combos_code[$i][1]),
                new Url(
                    'entity.site_info.edit_form', [
                        'site_info' => $sic_id,
                        'destination' => '/admin/rp/entity-content/site'
                    ]
                )
            );
        }
      }
    }
    return $site_links;

  }

  public function getSiteInfo($entity) {
    $query = \Drupal::service('entity.query')->get('site_info');

    $query->condition('field_site_info_site', $entity->id());

    $ids = $query->execute();

    if (empty($ids)) {
      return [];
    }
    else {
      $storage = \Drupal::entityManager()->getStorage('site_info');
      return $storage->loadMultiple($ids);
    }

  }

  public function getSiteCombos($entity) {
    $regions = []; $languages = []; $combos = []; $regions_code = []; $languages_code = [];
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
    foreach($regions as $i => $region_id) {
      foreach($languages as $j => $language_id) {
        $combos[] = [$region_id, $language_id];
        $combos_code[] = [$regions_code[$i],$languages_code[$j]];
      }
    }
    return ['combos' => $combos, 'combos_code' => $combos_code];
  }

  public function buildSiteInfoLabelLink($region,$language,$edit = true, $label_print = true) {
    $label = '';
    if($label_print) {
      $label = ($edit) ? t('Edit') : t('Create');
      $label .= ': ';
    }
    return $label . t('Region @region / Language @language',['@region'=>$region,'@language'=>$language]);
  }

}
