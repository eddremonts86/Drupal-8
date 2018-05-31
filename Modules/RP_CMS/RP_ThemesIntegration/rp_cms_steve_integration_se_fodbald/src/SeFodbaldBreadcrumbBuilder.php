<?php

/**
 * @file
 * Contains Drupal\rp_cms_steve_integration_se_fodbald\SeFodbaldBreadcrumbBuilder.
 */

namespace Drupal\rp_cms_steve_integration_se_fodbald;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Class SeFodbaldBreadcrumbBuilder.
 *
 * @package Drupal\rp_cms_steve_integration_se_fodbald
 */
class SeFodbaldBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    return !\Drupal::service('router.admin_context')->isAdminRoute();
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
	$links = []; 
    $breadcrumb = new Breadcrumb();
    $routs = ['rp_cms_steve_integration_se_fodbald.se_fodbald_providers', 'rp_cms_steve_integration_se_fodbald.se_fodbald_program', 'rp_cms_steve_integration_se_fodbald.se_fodbald_leagues'];
    
    $links[] = Link::createFromRoute(t('Forside'), '<front>');
    
    if(\Drupal::service('path.matcher')->isFrontPage()){
	   $links = [Link::createFromRoute(t('Se fodbold live | Live stream fodbold'), '<front>')];
    }else if(in_array($route_match->getRouteName(), $routs)){
	    $links[] = Link::createFromRoute($route_match->getRouteObject()->getDefault('_title'), $route_match->getRouteName());
    }else if($route_match->getRouteName() == 'entity.node.canonical'){
	    $entity = $route_match->getParameter('node');
	    $links[] = Link::createFromRoute($entity->title->value, $route_match->getRouteName(), ['node' => $entity->id()]);
    }else if($route_match->getRouteName() == 'entity.taxonomy_term.canonical'){
	    $tid = \Drupal::routeMatch()->getRawParameter('taxonomy_term');
		$term = \Drupal\taxonomy\Entity\Term::load($tid);
		
		if($term->getVocabularyId() == 'sport'){
			$links[] = Link::createFromRoute(t('Fodboldligaer'), 'rp_cms_steve_integration_se_fodbald.se_fodbald_leagues');
		}
		
		$links[] = Link::createFromRoute($term->name->value, $route_match->getRouteName(), ['taxonomy_term' => $tid]);
    }else{
	    $request = \Drupal::request();
		$title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());
		$links[] = Link::createFromRoute($title, $route_match->getRouteName());
    }
    
    $breadcrumb->setLinks($links);

    return $breadcrumb;
  }

}