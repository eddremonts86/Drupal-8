<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 1:20 PM
 */
namespace Drupal\rp_repo\Controller\entities\Pages;

use Drupal\Core\Controller\ControllerBase;

use Drupal\rp_repo\Controller\entities\Pages\pages;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyTournament;
use Drupal\rp_repo\Controller\entities\Generales\support;


class sportBlogPages extends pages
{
  public function createSportInternBlog($sportTags, $sportName, $name, $id, $url, $sportApiId, $type, $region)
  {

    if ($type) {
      $taxonomyTournament = new taxonomyTournament();
     $createStreamPages = [
        'type' => $type,
        'title' => $name,
        'field_sport_blogs_sport' => $sportTags,
        'field_sport_theme_blog_propertie' => '',
        'field_jsonld_struct' => ($taxonomyTournament->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => 'Blog')))->id(),
        'body' => [
          'value' => '',
          'summary' => '',
          'format ' => 'full_html',
        ],
        // 'path' => '/'.$getInfoObj->getClearUrl($name),
      ];
      $id = $this->createNodeGeneric($createStreamPages);
      print ' Creating Sport Internal Blog Page - ' . $name . '- at ' . date("h:i:s") . "\n";
    }
    return $id;

  }

    public function createSportInternPages($sportTags, $sportName, $PageTitle, $id, $url, $sportApiId, $type, $region)
    {
        if ($type) {
            $taxonomyTournament = new taxonomyTournament();
            $createStreamPages = [
                'type' => $type,
                'title' => $PageTitle,
                'field_sport_stream_reviews_sport' => $sportTags,
                'field_sport_review_properties' => '',
                'field_jsonld_struct' => ($taxonomyTournament->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => 'Reviews')))->id(),
                'body' => [
                    'value' => '',
                    'summary' => '',
                    'format ' => 'full_html',
                ]
            ];
            $id = $this->createNodeGeneric($createStreamPages);
            print ' Creating Sport Intern Pages - ' . $PageTitle . ' - at ' . date("h:i:s") . "\n";
        }
        return $id;

    }

}
