<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 1:22 PM
 */

namespace Drupal\rp_repo\Controller\entities\Taxonomies;

use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomy;
use Drupal\rp_repo\Controller\entities\Generales\images;
use Drupal\rp_repo\Controller\entities\Generales\support;
use Drupal\rp_repo\Controller\entities\Pages\sportPages;
use Drupal\rp_api\RPAPIClient;
use Drupal\node\Entity\Node;
use Drupal\rp_repo\Controller\entities\Generales\menu;
use Drupal\rp_repo\Controller\oldVersion\CreateClientData;


class taxonomyTournament extends taxonomy {

  /**
   * function createStreamPages ()
   * - $competition - Array  from Steve API
   *   competition: {
   * id: 153,
   * meta: [ {channel: 1},
   * {channel: 2},
   * {channel: 3},
   * {channel: 4}
   * ]
   *              }
   *
   *
   * - $sport_tags - Taxonomy ID to sport  (Example. 2145)
   * - $sportName - Sport name (Example. Tennis )
   */
  public function createTournamentPages($competition, $sport_tags, $sportName) {
    $taxonomyCompetition = $this->getTaxonomyByCriterio($competition['id'], 'field_competition_id');
    if (empty($taxonomyCompetition)) {
      $rpClient = RPAPIClient::getClient();
      $parameters = [
        'id' => $competition['id'],
        'include_locales' => 1,
      ];
      $competition = $rpClient->getCompetitionsbyID($parameters);
      $competitionName = $competition["name"];
      $logo_path = $competition["logo"];
      $logo_modified = $competition["modified"];
      $locale = json_encode($competition["locales"]);
      $imgObj = new images();
      $supportObj = new support();
      $logo = $imgObj->getImg($logo_path, $supportObj->getClearUrl($competitionName . '_logo'));
      $competition_array[0] = $competition;
      $NewTax = $this->createTournametTaxonomy($competition_array, 'sport', $sport_tags, $sportName, $logo, $logo_modified, $locale);
      return $NewTax;
    }
    else {
      $node = $taxonomyCompetition->id();
    }
    return $node;

  }

  /**
   * function createTournametTaxonomy ()
   * - $tournament (array from Steve API)
   * - $voc (vocavulary id)
   * - $sport_tags (name of item)
   * - $sport_id (sport taxonomy id)
   * - $logo (img id)
   * - $logo_modified (date of modified logo)
   * return taxonomy id
   */
  public function createTournametTaxonomy($tournament, $voc, $sport_tags, $sport_id, $logo, $logo_modified, $locale = []) {

    $tournamentId = $tournament[0]["id"];
    $tournamentName = $tournament[0]["name"];
    $tournamentParent = $tournament[0]["parent"];
    $tournamentSport = $tournament[0]["sport"];
    //  $supportObj = new support();
    $taxonomy = $this->getTaxonomyByAPIID($tournamentId);
    if (isset($taxonomy) or $taxonomy == FALSE) {
      if ($tournamentParent == NULL) {
        $taxonomySport = $this->getTaxonomyByAPIID('sport_' . 'sport_' . $tournamentSport);
        $obj = [
          'parent' => [$sport_tags],
          'name' => $tournamentName,
          'vid' => $voc,
          'field_api_id' => $tournamentId,
          'field_api_parent' => '',
          'field_competition_id' => $tournamentId,
          'field_content' => '',
          'field_locales' => $locale,
          'field_logo' => $logo,
          'field_logo_date' => $logo_modified,
          'field_sport_api_id' => $taxonomySport->id(),
          'field_jsonld_struct' => ($this->getTaxonomyByCriterioMultiple([
            'vid' => 'jsonld_',
            'name' => 'Leages',
          ]))->id(),
        ];
        $tournamentTaxonomy = $this->createGenericTaxonomy($obj, FALSE);
        return $tournamentTaxonomy->id();
      }
      else {
        $competition = $this->getTaxonomyParentRecursive($tournament);
        /**/
        $index = count($competition) - 2;
        if ($index < 0) {
          $index = 0;
        }
        for ($i = $index; $i >= 0; $i--) {
          $id = $competition[$i]["id"];
          $name = $competition[$i]["name"];
          $tournamentId = $competition[$i]["id"];
          $parent_id = $competition[$i]["parent"];
          $parent_locale = json_encode($competition[$i]["locales"]);
          $taxonomy = $this->getTaxonomyByAPIID($id);
          if (empty($taxonomy) or $taxonomy == FALSE) {
            if ($parent_id != NULL) {
              $parentTaxonomyId = $competition[$i + 1]["id"];
              $parentTaxonomyId = $this->getTaxonomyByAPIID($parentTaxonomyId);
              $parentTaxonomyId = $parentTaxonomyId->id();
              $obj = [
                'parent' => [$parentTaxonomyId],
                'name' => $name,
                'vid' => $voc,
                'field_api_id' => $tournamentId,
                'field_api_parent' => $parentTaxonomyId,
                'field_sport_api_id' => $sport_id,
                'field_competition_id' => $tournamentId,
                'field_content' => '',
                'field_logo' => $logo,
                'field_locales' => $parent_locale,
                'field_logo_date' => $logo_modified,

                'field_sport_api_id' => $sport_tags,
                'field_jsonld_struct' => ($this->getTaxonomyByCriterioMultiple([
                  'vid' => 'jsonld_',
                  'name' => 'Leages',
                ]))->id(),
              ];
              $this->createGenericTaxonomy($obj);
            }
            else {
              $obj = [
                'parent' => [$sport_tags],
                'name' => $name,
                'vid' => $voc,
                'field_api_id' => $tournamentId,
                'field_api_parent' => 'Sport id: ' . $sport_id,
                'field_sport_api_id' => $sport_id,
                'field_competition_id' => $tournamentId,
                'field_content' => '',
                'field_logo' => $logo,
                'field_locales' => $parent_locale,
                'field_logo_date' => $logo_modified,
                'field_sport_api_id' => $sport_tags,
                'field_jsonld_struct' => ($this->getTaxonomyByCriterioMultiple([
                  'vid' => 'jsonld_',
                  'name' => 'Sport',
                ]))->id(),
              ];
              $this->createGenericTaxonomy($obj);
            }
          }
        }
        /**/


      }
    }
    print ' Creating Tournament Taxonomy - ' . $tournamentName . ' - at ' . date("h:i:s") . "\n";
    $tournamentID = $competition[0]["id"];
    $tournamentTaxonomy = $this->getTaxonomyByAPIID($tournamentID);
    return $tournamentTaxonomy->id();
  }

  public function updateTournament($competitionId) {
    $rpClient = RPAPIClient::getClient();
    $parameters = ['id' => $competitionId];
    $competition = $rpClient->getCompetitionsbyID($parameters);
    $logo_path = $competition[0]["logo_path"];
    $logo_modified = $competition[0]["logo_modified"];
    $competitionName = $competition[0]["name"];
    $getObj = new RepoGeneralGetInfo();
    $node = $getObj->getNode($competitionId, 'tournament_page', 'field_tournament_api_id');
    $node = reset($node);
    if (strtotime($logo_modified) != strtotime($node->field_logo_date->value)) {
      $newLogo = $getObj->getImg($logo_path, $competitionName . '_logo');
      $node->title = $competitionName;
      $node->field_logo_date = $logo_modified;
      $node->field_logo = $newLogo;
      $node->save();
      print 'Update Tournament -' . $competitionName . ' - at ' . date("h:i:s") . "\n";
    }
    return $competitionId;
  }

  public function createTournamentAndParent($apiID) {
   $tournamentId = $this->getTaxonomyByOBj([
      'vid' => 'sport',
      'field_api_id' => $apiID,
    ], 1);

    if (!$tournamentId) {
      $rpClient = RPAPIClient::getClient();
      $imgObj = new images();
      $supportObj = new support();
      $parameters = ['id' => $apiID, 'include_locales' => 1];
      $newTournament = $rpClient->getCompetitionsbyID($parameters);
      $tournament[] = [
        "id" => $newTournament['id'],
        "name" => $newTournament['name'],
        "parent" => $newTournament['parent'],
        "locales" => $newTournament['locales'],
        "logo" => $newTournament['logo'],
        "sport" => $newTournament['sport'],
      ];
      $TaxonomyTree = array_reverse($this->getTaxonomyParentRecursive($tournament));


      foreach ($TaxonomyTree as $tournament) {
        if ($tournament['parent'] == NULL and $tournament['sport'] == NULL) {
          $sportDrupalId = 'sport_' . $tournament['id'];
          $spoertParentID = $this->getTaxonomyByOBj([
            'vid' => 'sport',
            'field_api_id' => $sportDrupalId,
          ], 'obj');
          if ($spoertParentID == FALSE) {
            $jsonld_struct = $this->getTaxonomyByCriterioMultiple(['vid' => 'jsonld_', 'name' => 'Sport'])->id();
            $obj = [
              'vid' => 'sport',
              'name' => $tournament['name'],
              'field_api_id' => 'sport_' . $tournament['id'],
              'field_competition_id' => $sportDrupalId,
              'field_content' => '',
              'parent' => '',
              'field_api_parent' => $tournament['id'],
              'field_locales' => json_encode($tournament['locales']),
              'field_logo' => $imgObj->getImg($tournament["logo"], $supportObj->getClearUrl($tournament['name'] . '_logo')),
              'field_logo_date' => $tournament["modified"],
              'field_sport_api_id' => $spoertParentID,
              'field_jsonld_struct' => $jsonld_struct,
              'field_base_color' =>  sprintf('#%06X', mt_rand(0, 0xFFFFFF))
            ];
            $taxonomySportId = $this->createGenericTaxonomy($obj, false);

            $pageOBJ = array(
                            'sportName' => $tournament['name'],
                            'taxonomySportId'=> $taxonomySportId->id(),
                            'region'=> strval(\Drupal::languageManager()->getDefaultLanguage()->getId()) ,
                            'json'  => $jsonld_struct,
                            'sportDrupalId' => $sportDrupalId
                      );
            $creatorObj = new CreateClientData();
            $creatorObj->createSportPages($pageOBJ);

          }
          else {
            continue;
          }
        }
        if ($tournament['parent'] == NULL and $tournament['sport'] != NULL) {
          $tournamentId = $this->getTaxonomyByOBj([
            'vid' => 'sport',
            'field_api_id' => $tournament['id'],
          ], 'obj');
          if ($tournamentId == FALSE) {

            $spoertParentID = $this->getTaxonomyByOBj([
              'vid' => 'sport',
              'field_api_id' => 'sport_' . $tournament['sport'],
            ], 1);

            $obj = [
              'vid' => 'sport',
              'name' => $tournament['name'],
              'field_api_id' => $tournament['id'],
              'field_competition_id' => $tournament['id'],
              'field_content' => '',
              'parent' => $spoertParentID,
              'field_api_parent' => $spoertParentID,
              'field_sport_api_id' => $spoertParentID,
              'field_locales' => json_encode($tournament['locales']),
              'field_logo' => $imgObj->getImg($tournament["logo"], $supportObj->getClearUrl($tournament['name'] . '_logo')),
              'field_logo_date' => $tournament["modified"],
              'field_jsonld_struct' => (
              $this->getTaxonomyByCriterioMultiple(
                ['vid' => 'jsonld_', 'name' => 'Sport']))->id(),
            ];
            $this->createGenericTaxonomy($obj, FALSE);
          }
          else {
            continue;
          }
        }
        if ($tournament['parent'] != NULL and $tournament['sport'] != NULL) {
          $tournamentId = $this->getTaxonomyByOBj([
            'vid' => 'sport',
            'field_api_id' => $tournament['id'],
          ], 1);
          if ($tournamentId == FALSE) {
            $ParentID = $this->getTaxonomyByOBj([
              'vid' => 'sport',
              'field_api_id' => $tournament['parent'],
            ], 1);
            $sportParentID = $this->getTaxonomyByOBj([
              'vid' => 'sport',
              'field_api_id' => 'sport_' . $tournament['sport'],
            ], 1);
            $obj = [
              'vid' => 'sport',
              'name' => $tournament['name'],
              'field_api_id' => $tournament['id'],
              'field_competition_id' => $tournament['id'],
              'field_content' => '',
              'parent' => $ParentID,
              'field_api_parent' => $ParentID,
              'field_locales' => json_encode($tournament['locales']),
              'field_logo' => $imgObj->getImg($tournament["logo"], $supportObj->getClearUrl($tournament['name'] . '_logo')),
              'field_logo_date' => $tournament["modified"],
              'field_sport_api_id' => $sportParentID,
              'field_jsonld_struct' => (
              $this->getTaxonomyByCriterioMultiple(
                ['vid' => 'jsonld_', 'name' => 'Sport']))->id(),
            ];
            $this->createGenericTaxonomy($obj, FALSE);
          }

        }
      }
      $tournamentId = $this->getTaxonomyByOBj([
        'vid' => 'sport',
        'field_api_id' => $apiID,
      ], 1);
      return $tournamentId;
    }
    else
      return $tournamentId;
  }

  public
  function creteSport($id) {
    $sportID = 'sport_' . $id;
    $sportIdTAxonomy = $this->getTaxonomyByAPIID($sportID)->tid->value;
    if (!$sportIdTAxonomy) {
      /*$obj = [
          'parent' => [$sport_tags],
          'name' => $tournamentName,
          'vid' => $voc,
          'field_api_id' => $tournamentId,
          'field_api_parent' => '',
          'field_competition_id' => $tournamentId,
          'field_content' => '',
          'field_locales' => $locale,
          'field_logo' => $logo,
          'field_logo_date' => $logo_modified,
          'field_sport_api_id' => $taxonomySport->id(),
          'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => 'Leages')))->id(),

      ];*/
    }
    return $sportIdTAxonomy;
  }

}
