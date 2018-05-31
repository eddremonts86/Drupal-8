<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 1:19 PM
 */

namespace Drupal\rp_repo\Controller\entities\Pages;

use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\entities\Pages\pages;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyTournament;
use Drupal\rp_repo\Controller\entities\Generales\support;

use Drupal\rp_repo\Controller\entities\Generales\images;
use Drupal\rp_repo\Controller\entities\Generales\translations;
use Drupal\rp_repo\Controller\entities\Generales\menu;

class sportPages extends pages
{
    /**
     * Funtion createSportPages ( create a Taxonomy,sport pagas to a specify sport )
     * - $sportDrupalId (drupal specify id for sports - Example. sport_1 )
     * - $sportApiId ( Steve API Sport id -  Example. 1 )
     * - $region ( Steve API region - Example. en_EG)
     */
    public function createSportPages($sportDrupalId, $sportApiId, $region, $color = "")
    {
        $rpClient = RPAPIClient::getClient();
        $getInfoObj = new taxonomyTournament();
        $support = new support();
        $menu = new menu();
        $translations = new translations();
        $parameters = ['id' => $sportApiId, 'include_locales' => 1];
        $obj = ['field_api_id' => $sportDrupalId];
        $TaxonomyObj = $getInfoObj->getTaxonomyByCriterioMultiple($obj);
        if (empty($TaxonomyObj)) {
            $sport = $rpClient->getSportbyID($parameters);
            $sportName = $sport["name"];
            $locales = json_encode($sport["locales"]);
            $taxonomySportId = $getInfoObj->defaultTournamentTaxonomy('', $sportName, 'sport', $sportDrupalId, 'null', $sportDrupalId, 'Sport', $color, $locales);
            $node = [
                'type' => 'sport',
                'title' => $sportName,
                'field_sport_sport' => $taxonomySportId,
                'field_sport_theme_properties' => '',
                'langcode' => $region,
                'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => 'Sport')))->id(),
                'body' => [
                    'value' => '',
                    'summary' => '',
                    'format ' => 'full_html',
                ],
            ];
            $newNode = $this->createNodeGeneric($node);
            $menu->createItemMenu('main', '', $newNode, $sportName, $taxonomySportId, $sportDrupalId, $region);
            print 'Creating Sport Pages -' . $sportName . ' - at ' . date("h:i:s") . "\n";
        } else {
            $competitionName = $TaxonomyObj->name->value;
            $taxonomySportId = $TaxonomyObj->id();

            $sportNode = reset($this->getNode($taxonomySportId, 'sport', 'field_sport_sport'));
            $translations->createTranslation($sportNode, $region, [], '/' . $region . '/' . $sportNode->getTitle());

            $sportNode = reset($this->getNode($taxonomySportId, 'sport_blogs', 'field_sport_blogs_sport'));
            $translations->createTranslation($sportNode, $region, [], '/' . $region . '/' . $sportNode->getTitle());

            $sportNode = reset($this->getNode($taxonomySportId, 'sport_stream_reviews', 'field_sport_stream_reviews_sport'));
            $translations->createTranslation($sportNode, $region, [], '/' . $region . '/' . $sportNode->getTitle());

            print "\n";
            print ' Using Sport - ' . $competitionName . ' ( ' . $region . ' ) - at ' . date("h:i:s") . "\n";
        }
        return $taxonomySportId;
    }


}
