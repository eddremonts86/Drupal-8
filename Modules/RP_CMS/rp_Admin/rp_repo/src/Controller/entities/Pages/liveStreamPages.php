<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 1:20 PM
 */

namespace Drupal\rp_repo\Controller\entities\Pages;

use Drupal\rp_repo\Controller\entities\Pages\pages;

class liveStreamPages extends pages
{
  public function createSportInternPages($sportTags, $sportName, $PageTitle, $id, $url, $sportApiId, $type, $region)
  {
    if ($type) {
      $getInfoObj = new RepoGeneralGetInfo();
      $Aliasdata = $region . '/' . $getInfoObj->getClearUrl($sportName) . '/' . $getInfoObj->getClearUrl($url);
      $alias = $getInfoObj->getMultiplesAlias($Aliasdata);
      $createStreamPages = [
        'type' => $type,
        'title' => $PageTitle,
        'field_sport_stream_reviews_sport' => $sportTags,
        'field_sport_review_properties' => '',
        'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => 'Reviews')))->id(),
        'body' => [
          'value' => '',
          'summary' => '',
          'format ' => 'full_html',
        ],
        //'path' => '/'.$getInfoObj->getClearUrl($PageTitle),
      ];
      $id = $this->createNodeGeneric($createStreamPages);
      print ' Creating Sport Intern Pages - ' . $PageTitle . ' - at ' . date("h:i:s") . "\n";
    }
    return $id;

  }

}
