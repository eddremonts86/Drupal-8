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


abstract class taxonomy extends ControllerBase
{
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
    public function createGenericTaxonomy($obj, $reset = true)
    {
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
    public function createVocabulary()
    {
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
    public function createTaxonomyByNameAndVoc($name, $voc)
    {
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

    public function getTaxonomyName($name, $vid)
    {
        $taxonomy = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $name, 'vid' => $vid]);
        return $taxonomy;
    }

    /**
     * function createChannelsPages ( )
     *
     */
    public function defaultTournamentTaxonomy($parentId, $name, $voc, $tournamentId, $tournamentParents, $sport_id, $jsonld = 'Leages', $color, $locales = [])
    {
        $taxonomy = $this->getTaxonomyByAPIID($tournamentId);
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
                    'field_jsonld_struct' => ($this->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => $jsonld)))->id(),
                ]);
            } else {
                $term = Term::create([
                    'parent' => [$parentId],
                    'name' => $name,
                    'vid' => $voc,
                    'field_api_id' => $tournamentId,
                    'field_api_parent' => $tournamentParents,
                    'field_sport_api_id' => $sport_id,
                    'field_base_color' => $color,
                    'field_jsonld_struct' => ($this->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => $jsonld)))->id(),
                ]);
            }

            $term->save();
            print ' Creating Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
            print ' Creating Tournament Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
            $taxonomy = $this->getTaxonomyByAPIID($tournamentId);
        }
        return $taxonomy->id();

    }

    public function getTaxonomyByID($entityId)
    {
        $taxonomy = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['tid' => $entityId]);
        return reset($taxonomy);
    }

    public function getTaxonomyByAPIID($apiId, $reset = true)
    {
        $taxonomy = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['field_api_id' => $apiId]);
        if (!empty($taxonomy)) {
            if (!$reset) {
                return $taxonomy;
            } else {
                return reset($taxonomy);
            }
        } else {
            return false;
        }


    }

    public function getTaxonomyByParticipantAPIID($id)
    {
        $taxonomy = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['field_participant_api_id' => $id]);
        return reset($taxonomy);
    }

    public function getTaxonomyByCriterionMultiple($obj, $reset = 0)
    {
        $taxonomy = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties($obj);
        if ($reset != 0) {
            return $taxonomy;
        } else {
            return reset($taxonomy);
        }


    }

    public function getTaxonomyByCriterio($fieldData, $field)
    {
        $taxonomy = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties([$field => $fieldData]);
        return reset($taxonomy);
    }

    public function getTaxonomyParentRecursive($competitionArray)
    {
        $rpClient = RPAPIClient::getClient();
        $index = count($competitionArray) - 1;
        $tournamentParent = $competitionArray[$index]["parent"];
        if ($tournamentParent != null) {
            $parameters = [
                'id' => $tournamentParent,
                'include_locales' => 1
            ];
            if ($tournamentParent) {
                $CompetitionParent = $rpClient->getCompetitionsbyID($parameters);
                $competitionArray[count($competitionArray)] = $CompetitionParent;
                $competitionArray = $this->getTaxonomyParentRecursive($competitionArray);
                return $competitionArray;
            } else {
                $parameters = [
                    'id' => $competitionArray[$index]["sport"],
                    'include_locales' => 1
                ];
                $sport = $rpClient->getSportbyID($parameters);
                $competitionArray[count($competitionArray)] = $sport;
                return $competitionArray;
            }
        }
        return $competitionArray;
    }

    public function getTaxonomyByOBj($obj)
    {
        $taxonomy = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties($obj);
        return $taxonomy->id();


    }

    public function getTaxonomyByCriterioMultiple($obj, $reset = 0)
    {
        $taxonomy = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties($obj);
        if ($reset != 0) {
            return $taxonomy;
        } else {
            return reset($taxonomy);
        }


    }


    /*   -----   Delete Process   ------   */
    public function updateTaxonomy($obj)
    {
    }


    /*   -----   Delete Process   ------   */
    public function deleteTaxonomy($id)
    {
        $taxonomy = Term::load($id);
        $taxonomy->delete();
        return true;
    }

    public function deleteTaxonomyByVocabulary($voc)
    {
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
        return true;

    }

    public function deleteTaxonomyItems()
    {
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
        return true;

    }
}
