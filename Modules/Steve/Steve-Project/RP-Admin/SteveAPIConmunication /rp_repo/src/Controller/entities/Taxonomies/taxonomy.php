<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 3:24 PM
 */


namespace Drupal\rp_repo\Controller\entities\Taxonomies;

use Drupal\Core\Controller\ControllerBase;
use Cocur\Slugify\Slugify;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\rp_api\RPAPIClient;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;


abstract class taxonomy extends ControllerBase {

  /**
   * function createGenericTaxonomy ( )
   * - $obj (array of fields an data)
   * $obj = [
   * 'parent' => [],
   * 'name' => $name,
   * 'vid' => $voc,
   * 'field_participant_api_id' => $idApi,
   * ]
   * return an object or an array if $reset is true
   */
  public function createGenericTaxonomy($obj, $reset = TRUE) {
    $term = Term::create($obj);
    $term->save();
    if ($reset) {
      $term = reset($term);
    }
    return $term;
  }

  /**
   * function createVocabulary
   *
   */
  public function createVocabulary() {
    $vocabularys = ['sport', 'stream_provider', 'participant', 'tournament'];
    foreach ($vocabularys as $vocal) {
      $vocabulary = \Drupal\taxonomy\Entity\Vocabulary::create([
        'vid' => $vocal,
        'description' => '',
        'name' => $vocal,
      ]);
      print ' Creating Vocabulary -' . $vocal . ' - at ' . date("h:i:s") . "\n";
      $vocabulary->save();
    }
    return TRUE;

  }

  /**
   * function createTaxonomy ( )
   * - $name (name of item)
   * - $voc (vocavulary id)
   * return an array
   */
  public function createTaxonomyByNameAndVoc($name, $voc) {
    $taxonomy = $this->getTaxonomyName($name, $voc);
    if (!$taxonomy) {
      $term = Term::create([
        'parent' => [],
        'name' => $name,
        'vid' => $voc,
      ]);
      $term->save();
      $taxonomy = $this->getTaxonomyName($name, $voc);
      print ' Creating Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
    }
    $term = reset($taxonomy);
    return $term;

  }

  public function getTaxonomyName($name, $vid) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $name, 'vid' => $vid]);
    return $taxonomy;
  }

  /**
   * function createChannelsPages ( )
   *
   */
  public function defaultTournamentTaxonomy($parentId, $name, $voc, $tournamentId, $tournamentParents, $sport_id, $jsonld = 'Leages', $color, $locales = []) {

    $obj = ['vid' => $voc, 'field_api_id' => $tournamentId];
    $taxonomy = $this->getTaxonomyByOBj($obj, 'obj');
    $taxonomy = reset($taxonomy);
    if ($taxonomy == FALSE) {
      if ($voc = 'sport') {
        $term = Term::create([
          'parent' => [$parentId],
          'name' => $name,
          'vid' => $voc,
          'field_api_id' => $tournamentId,
          'field_api_parent' => $tournamentParents,
          'field_sport_api_id' => $sport_id,
          'field_base_color' => $color,
          'field_locales' => $locales,
          'field_jsonld_struct' => $this->getTaxonomyByOBj([
            'vid' => 'jsonld_',
            'name' => $jsonld,
          ], 1),
        ]);
      }
      else {
        $term = Term::create([
          'parent' => [$parentId],
          'name' => $name,
          'vid' => $voc,
          'field_api_id' => $tournamentId,
          'field_api_parent' => $tournamentParents,
          'field_sport_api_id' => $sport_id,
          'field_base_color' => $color,
          'field_jsonld_struct' => $this->getTaxonomyByCriterioMultiple([
            'vid' => 'jsonld_',
            'name' => $jsonld,
          ], 1),
        ]);
      }
      $term->save();
      print ' Creating Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
      print ' Creating Tournament Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
      $obj = ['vid' => $voc, 'field_api_id' => $tournamentId];
      $taxonomy = $this->getTaxonomyByOBj($obj, 'obj');
      $taxonomy = reset($taxonomy);
    }
    return $taxonomy->id();

  }

  public function getTaxonomyByID($entityId) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['tid' => $entityId]);
    return reset($taxonomy);
  }

  public function getTaxonomyByAPIID($apiId, $reset = TRUE) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['field_api_id' => $apiId]);
    if (!empty($taxonomy)) {
      if (!$reset) {
        return $taxonomy;
      }
      else {
        return reset($taxonomy);
      }
    }
    else {
      return FALSE;
    }


  }

  public function getTaxonomyByParticipantAPIID($id) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['field_participant_api_id' => $id]);
    return reset($taxonomy);
  }

  public function getTaxonomyByCriterionMultiple($obj, $reset = 0) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties($obj);
    if ($reset != 0) {
      return $taxonomy;
    }
    else {
      return reset($taxonomy);
    }


  }

  public function getTaxonomyByCriterio($fieldData, $field) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties([$field => $fieldData]);
    return reset($taxonomy);
  }

  public function getTaxonomyParentRecursive($competitionArray) {
    $rpClient = RPAPIClient::getClient();
    $index = count($competitionArray) - 1;
    $tournamentParent = $competitionArray[$index]["parent"];
    if ($tournamentParent != NULL) {
      $parameters = [
        'id' => $tournamentParent,
        'include_locales' => 1,
      ];
      $CompetitionParent = $rpClient->getCompetitionsbyID($parameters);
      $competitionArray[count($competitionArray)] = $CompetitionParent;
      $competitionArray = $this->getTaxonomyParentRecursive($competitionArray);
      return $competitionArray;

    }
    else {
      $parameters = [
        'id' => $competitionArray[$index]["sport"],
        'include_locales' => 1,
      ];
      $sport = $rpClient->getSportbyID($parameters);
      $competitionArray[] = $sport;
      return $competitionArray;
    }

    return $competitionArray;
  }

  public function getTaxonomyByOBj($obj, $reset = 0) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties($obj);
    if ($taxonomy) {
      if ($reset != 0) {
        return reset($taxonomy)->id();
      }
      else {
        if ($reset == 'obj') {
          return $taxonomy;
        }
        else {
          return $taxonomy->id();
        }
      }

    }
    else {
      return FALSE;
    }

  }

  public function getTaxonomyByCriterioMultiple($obj, $reset = 0) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties($obj);
    if ($reset != 0) {
      return $taxonomy;
    }
    else {
      return reset($taxonomy);
    }


  }

  public function getNamebyId($obj) {
    $taxonomy = $this->getTaxonomyByOBj($obj, 'obj');
    return reset($taxonomy)->name->value;
  }

  public function getNamebyAPIId($obj) {
    $taxonomy = $this->getTaxonomyByOBj($obj, 'obj');
    return reset($taxonomy)->name->value;
  }

  public function getLocaleName($localeObj, $id) {
    foreach ($localeObj as $local) {
      if ($local['language'] == $id) {
        return $local['name'];
      }
    }

  }

  /*   -----   Delete Process   ------   */
  public function updateTaxonomy($obj) {
  }

  /*   -----   Delete Process   ------   */
  public function deleteTaxonomy($id) {
    $taxonomy = Term::load($id);
    $taxonomy->delete();
    return TRUE;
  }

  public function deleteTaxonomyByVocabulary($voc) {
    $obj = ['vid' => $voc];
    $query = \Drupal::entityQuery('taxonomy_term')->loadByProperties($obj);
    $ids = $query->execute();
    foreach ($ids as $id) {
      $taxonomy = Term::load($id);
      if (!empty($taxonomy)) {
        $taxonomy->delete();
      }
      echo "Delete Taxonomy : " . $id;
      echo "\n";
    }
    return TRUE;

  }

  public function deleteTaxonomyItems() {
    $query = \Drupal::entityQuery('taxonomy_term');
    $ids = $query->execute();
    foreach ($ids as $id) {
      $taxonomy = Term::load($id);
      if (!empty($taxonomy)) {
        echo "Delete Taxonomy type '" . $taxonomy->getName() . "'";
        echo "\n";
        $taxonomy->delete();
      }
    }
    return TRUE;

  }


  public function createSiteConvination($site_api_id, $days) {
    $site = ['vid' => 'steve_site', 'field_api_id' => $site_api_id];
    $siteObj = reset($this->getTaxonomyByOBj($site, 'obj'));

    foreach ($siteObj->field_languages as $field_languages) {
      $languages = [
        'vid' => 'steve_languages',
        'tid' => $field_languages->target_id,
      ];
      $lan = reset($this->getTaxonomyByOBj($languages, 'obj'));
      $languagesName[] = [
        'code' => $lan->field_locale->value,
        'apiid' => $lan->field_api_id->value,
      ];
    }

    foreach ($siteObj->field_regions as $field_regions) {
      $region = ['vid' => 'steve_regions', 'tid' => $field_regions->target_id];
      $regionsName[] = reset($this->getTaxonomyByOBj($region, 'obj'))->field_code->value;
    }

    $paramList = [];

    $date = 'Y-m-d';
    $startday = date($date);

    for ($i = 0; $i < $days; $i++) {

      $newDate = strtotime('+' . $i . ' day', strtotime($startday));
      $date = date('Y-m-d', $newDate);

      foreach ($languagesName as $language) {
        foreach ($regionsName as $region) {
          $paramList [] = [
            'site' => $site_api_id,
            'region' => $region,
            'lang' => $language['code'],
            'langApiId' => $language['apiid'],
            'start' => $date,
            'include_participants' => 1,
            'tz' => date_default_timezone_get(),
            'include_locales' => 1,
          ];
        }
      }
    }
    return $paramList;

  }

}
