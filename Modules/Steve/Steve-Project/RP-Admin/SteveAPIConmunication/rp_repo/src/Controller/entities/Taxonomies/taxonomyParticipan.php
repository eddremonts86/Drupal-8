<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 1:22 PM
 */

namespace Drupal\rp_repo\Controller\entities\Taxonomies;

use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomy;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyTournament;
use Drupal\rp_repo\Controller\entities\Generales\support;
use Drupal\rp_repo\Controller\entities\Generales\images;
use Drupal\rp_repo\Controller\entities\Pages\sportPages;

class taxonomyParticipan extends taxonomy
{
    /**
     * function createParticipantPages ( )
     * - $partArray ( Participants array from Steve - API )
     *    participants: [{
     * id: 687,
     * meta: [
     * {channel: 1},
     * {channel: 2},
     * {channel: 3},
     * {channel: 4}
     * ],
     * name: "Trelleborgs FF",
     * running_order: 2
     * },{
     * id: 864,
     * meta: [
     * {channel: 1},
     * {channel: 2},
     * {channel: 3},
     * {channel: 4}
     * ],
     * name: "Örgryte IS",
     * running_order: 1
     * }
     * ]
     * -  $sport_tags ( Sport Taxonomy id )
     */
    public function createParticipantPages($partArray, $sport_tags)
    {
        $supportObj = new support();
        $imgObj = new images();
        $tags_team_array = [];
        foreach ($partArray as $team) {
            $id = $team['id'];
            $name = $team['name'];
            $opc = 'field_api_id';
            $taxonomyCompetition = $this->getTaxonomyByCriterio($id, $opc);
            if (empty($taxonomyCompetition)) {
                $parameters = ['id' => $team ['id'], 'include_locales' => 1];
                $rpClient = RPAPIClient::getClient();
                $Participants = $rpClient->getParticipantsbyID($parameters);
                $logo_path = $Participants["logo"];
                $logo_modified = $Participants["modified"];
                $locales = json_encode($Participants["locales"]);
                $logo = $imgObj->getImg($logo_path, $supportObj->getClearUrl($name . '_logo'), 'team');
                $data = $supportObj->getClearUrl(strtolower($name));
                $obj = [
                    'parent' => [],
                    'name' => $name,
                    'vid' => 'participant',
                    'field_api_id' => $team ['id'],
                    'field_participant_content' => '',
                    'field_participant_logo' => $logo,
                    'field_participant_logo_date' => $logo_modified,
                    'field_participant_sport' => $sport_tags,
                    'field_locales' => $locales,
                    'field_jsonld_struct' => ($this->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => 'Participants')))->id(),
                ];
                $this->createGenericTaxonomy($obj);
                $taxonomyCompetition = $this->getTaxonomyByCriterio($id, $opc);
                $team_tax = $taxonomyCompetition->id();
                print ' Creating Participant - ' . $team ['name'] . '- at ' . date("h:i:s") . "\n";
            } else {
                $team_tax = $taxonomyCompetition->id();

            }
            $tags_team_array [] = ['target_id' => $team_tax];
        }
        return $tags_team_array;
    }


    public function createParticipantByID($participantID)
    {
        $tags_team_array = [];
        $supportObj = new support();
        $imgObj = new images();
        $sportPages = new sportPages();
        $tornomentObj = new taxonomyTournament();
        $taxonomyCompetition = $this->getTaxonomyByCriterio($participantID, 'field_api_id');
        if (empty($taxonomyCompetition)) {
            $parameters = ['id' => $participantID, 'include_locales' => 1];
            $rpClient = RPAPIClient::getClient();
            $Participants = $rpClient->getParticipantsbyID($parameters);
            $logo_path = $Participants["logo"];
            $logo_modified = strtotime($Participants["modified"]);
            $locales = json_encode($Participants["locales"]);
            $logo = $imgObj->getImg($logo_path, $supportObj->getClearUrl($Participants["name"] . '_logo'), 'team');


            $sportDrupalId = 'sport_' . $Participants['sport'];
            $sportApiId = $Participants['sport'];
            if (isset($sportApiId)) {
                $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                $region = strval(\Drupal::languageManager()->getDefaultLanguage()->getId());
                $sportTags = $sportPages->createSportPages($sportDrupalId, $sportApiId, $region, $color);
            }
            $obj = [
                'parent' => [],
                'name' => $Participants["name"],
                'vid' => 'participant',
                'field_api_id' => $participantID,
                'field_participant_content' => '',
                'field_participant_logo' => $logo,
                'field_participant_logo_date' => $logo_modified,
                'field_participant_sport' => $sportTags,
                'field_locales' => $locales,
                'field_jsonld_struct' => ($this->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => 'Participants')))->id(),
            ];

            $this->createGenericTaxonomy($obj);
            $taxonomyCompetition = $this->getTaxonomyByCriterio($participantID, 'field_api_id');
            $team_tax = $taxonomyCompetition->id();
            print ' Creating Participant - ' . $taxonomyCompetition->name->value . '- at ' . date("h:i:s") . "\n";
        } else {
            $team_tax = $taxonomyCompetition->id();
        }
        return $team_tax;
    }


    /**
     * function createParticipantTaxonomy ( )
     * - $name (name of item)
     * - $voc (vocavulary id)
     * - $idApi (Participant API id)
     * return taxonomy id
     */
    public function createParticipantTaxonomy($name, $voc, $idApi, $locale)
    {

        $taxonomy = $this->getTaxonomyByParticipantAPIID($idApi);

        if (!$taxonomy or empty($taxonomy)) {
            $term = Term::create([
                'parent' => [],
                'name' => $name,
                'vid' => $voc,
                'field_locales' => $locale,
                'field_api_id' => $idApi,
                'field_jsonld_struct' => ($this->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => 'Participants')))->id(),
            ]);
            $term->save();
            $taxonomy = $this->getTaxonomyByParticipantAPIID($idApi);
        }
        print ' Creating Participant Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
        return $taxonomy->id();

    }

    public function updateParticipant($nodeId)
    {
        $supportObj = new support();
        $imgObj = new images();
        $rpClient = RPAPIClient::getClient();
        $parameters = ['id' => $nodeId];
        $Participants = $rpClient->getParticipantsbyID($parameters);
        $logo_modified = $Participants[0]["logo_modified"];
        $ParticipantsObj = Node::load($nodeId);
        $name = $Participants[0]["name"];
        $logoModified = strtotime($logo_modified);
        $fieldLogoDate = strtotime($ParticipantsObj->field_logo_participant_date->value);
        if ($logoModified != $fieldLogoDate) {
            $logo_path = $Participants[0]["logo_path"];
            $logo = $imgObj->getImg($logo_path, $supportObj->getClearUrl($name . '_logo'));
            $ParticipantsObj->field_participant_logo = $logo;
            $ParticipantsObj->field_logo_participant_date = $logo_modified;
            $ParticipantsObj->save();
            echo 'Update Participant -  ' . $name . ' - at ' . date("h:i:s") . "\n";
        }
        return TRUE;
    }


}
