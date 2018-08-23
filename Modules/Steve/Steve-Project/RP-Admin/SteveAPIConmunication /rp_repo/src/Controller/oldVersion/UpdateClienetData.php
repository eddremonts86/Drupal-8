<?php

namespace Drupal\rp_repo\Controller\oldVersion;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\rp_api\RPAPIClient;

/*
use Cocur\Slugify\Slugify;
use Drupal\rp_repo\Controller\oldVersion\RepoGeneralGetInfo;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\taxonomy\Plugin\views\argument\Taxonomy;
use Drupal\Core\Language\LanguageInterface;
*/

/**
 * Class UpdateClienetData.
 */
class UpdateClienetData extends ControllerBase
{

  public function updatePathauto()
  {
  }

  public function updateParticipant($nodeId)
  {
    $getObj = new RepoGeneralGetInfo();
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
      $logo = $getObj->getImg($logo_path, $getObj->getClearUrl($name . '_logo'));
      $ParticipantsObj->field_participant_logo = $logo;
      $ParticipantsObj->field_logo_participant_date = $logo_modified;
      $ParticipantsObj->save();
      echo 'Update Participant -  ' . $name . ' - at ' . date("h:i:s") . "\n";
    }
    return TRUE;
  }

  public function updateTournament($competitionId)
  {
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
      echo 'Update Tournament -' . $competitionName . ' - at ' . date("h:i:s") . "\n";
    }
    return $competitionId;
  }

  public function updateEvents($event, $node_id, $region)
  {
    $getInfoObj = new RepoGeneralGetInfo();
    $node = Node::load($node_id);
    $obj = [
      'vid' => 'sport',
      'tid' => $node->field_events_sport->target_id,
    ];
    $data = json_decode($node->field_events_properties->value);
    $sport = $getInfoObj->getTaxonomyByCriterioMultiple($obj, 0);
    $alias = '/'.$region.'/'.$getInfoObj->getClearUrl(reset($sport)["name"]["x-default"]).'/'.$getInfoObj->getClearUrl($event['name']);
    $ifAlias = $getInfoObj->getAlias('/node/'.$node_id, $alias);
    if (!isset($data->$region)) {
      $metaSportArray = $event['sport'];
      $metaCompetitionArray = $event['competition'];
      $metaEventArray = $event['meta'];
      $metaParticipantsArray = $event['participants'];
      $metaStreamproviderArray = $event['streamprovider'];
      $data->$region = [
        "sport" => $metaSportArray,
        "competition" => $metaCompetitionArray,
        "event" => $metaEventArray,
        "participants" => $metaParticipantsArray,
        "streamers" => $metaStreamproviderArray,
      ];
      $properties = json_encode($data);
      $this->createTranslation($node, $region, $properties, $alias);
    }
    return TRUE;
  }

  public function updateChanel($nodeId, $channel)
  {
    $node = Node::load($nodeId);
    $changed = FALSE;
    if (isset($node)) {

      if ($node->field_channel_description->value‎ != $channel['description'] and $channel['description'] != NULL) {
        $node->field_channel_description‎->value‎ = $channel['description'];
        $changed = TRUE;
      }
      if ($node->field_channel_notes->value‎ != $channel['notes'] and $channel['notes'] != NULL) {
        $node->field_channel_notes->value‎ = $channel['notes'];
        $changed = TRUE;
      }

      if ($changed == TRUE) {
        $node->save();
        echo 'Updating node (Channels) "' . $node->getTitle() . '- at ' . date("h:i:s") . "\n";
      }
    }
    return TRUE;
  }

  public function updateMetaByChanel()
  {
  }

  public function UpdateClientData()
  {
  }

  public function updateMainMenu()
  {

    $menu_link = \Drupal::entityTypeManager()
      ->getStorage('menu_link_content')
      ->loadByProperties(['menu_name' => 'main', 'weight' => '0']);

    foreach ($menu_link as $sport) {
      if ($sport->description->value != 'home') {
        $fromDate = strtotime(date("Y-m-d H:i:s", strtotime("midnight")));
        $endDate = strtotime(date("Y-m-d H:i:s", strtotime("tomorrow")));
        $ids = \Drupal::entityQuery('node')
          ->condition('status', 1)
          ->condition('promote', 1)
          ->condition('type', 'events')
          ->condition('field_events_sport', $sport->description->value)
          ->condition('field_event_date', $fromDate, '>')
          ->condition('field_event_date', $endDate, '<')
          ->range(0, 1);
        $event = count($ids->execute());
        if ($event == 0 or !isset($event)) {
          $sport->enabled->value = 0;
          $sport->save();
          var_dump($sport->title->value . " don't have future events, changed to desable");
        } else {
          if ($sport->enabled->value == 0 and $event > 0) {
            $sport->enabled->value = 1;
            $sport->save();
            var_dump($sport->title->value . " have future events, changed to enable");
          }
        }

      }
    }
    return TRUE;
  }

  public function UpdateClienetData()
  {
    return true;
  }

  public function generateAliasbyTrasnlations()
  {
    $language = \Drupal::languageManager()->getLanguages();
    $defaultLanguage = \Drupal::languageManager()->getDefaultLanguage()->getId();
    $query = \Drupal::entityQuery('taxonomy_term');
    $ids = $query->execute();
    foreach ($language as $land) {
      foreach ($ids as $id) {
        $l = $land->getId();
        if ($l != $defaultLanguage) {
          $term = Term::load($id);
          $translated_fields = $term;
          $translated_fields = $translated_fields->toArray();
          $include = ['sport', 'participant', 'stream_provider'];
          $vid = $translated_fields['vid'][0]['target_id'];
          if (in_array($vid, $include)) {
            if (!$term->hasTranslation($l)) {
              var_dump("Making translation to " . $l . " lang");
              $translated_entity_array = array_merge($term->toArray(), ['name' => $term->name->value . ' - ' . $l]);
              $term->addTranslation($l, $translated_entity_array)->save();
            }
          }
        }
      }
    }
    return true;
  }

  public function createTranslation($node, $region, $properties = [], $alias)
  {
    $defLand = \Drupal::languageManager()->getDefaultLanguage()->getId();
    if (!$node->hasTranslation($region) and $region != $defLand) {
      print "Making translation to " . $region . "lang";
      $node_translate = $node;
      if (!empty($properties)) {
        $newTranslation = [
          'title' => $node->getTitle() . ' -' . $region,
          'field_events_properties' => $properties
        ];
        $translated_entity_array = array_merge($node->toArray(), $newTranslation);
        $node_translate->addTranslation(strval($region), $translated_entity_array)->save();
        return true;
      }
      else {
        $newTranslation = [
          'title' => $node->getTitle() . ' -' . $region
        ];
        $translated_entity_array = array_merge($node->toArray(), $newTranslation);
        $node_translate->addTranslation(strval($region), $translated_entity_array)->save();
        print 'Title Translation ';
        return true;
      }
    }
  }
}
