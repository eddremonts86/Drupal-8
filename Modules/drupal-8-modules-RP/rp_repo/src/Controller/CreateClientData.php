<?php

namespace Drupal\rp_repo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\menu_link_content\Entity\MenuLinkContent;


use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\RepoGeneralGetInfo;
use Drupal\rp_repo\Controller\UpdateClienetData;

/**
 * Class CreateClientData.
 */
class CreateClientData extends ControllerBase {

    //------------------------------------------------------------ NODE CREATION ----------------------------

    public function createSportPages($sport_id)
    {
        $rpClient = RPAPIClient::getClient();
        $getInfoObj = new RepoGeneralGetInfo();
        $parameters = ['id' => $sport_id];
        $nodeObj = $getInfoObj->getNode($sport_id,'sport_pages', 'field_sport_api_id');
        if (empty($nodeObj)) {
            $sport = $rpClient->getSportbyID($parameters);
            $sportName = $sport["name"];
            $sportid = $sport["id"];
            $node = [
              'type' => 'sport_pages',
              'title' => $sportName,
              'field_sport_name' => $sportName,
              'field_sport_api_id' => $sportid,
              'field_sport_theme_descrption' => $getInfoObj->getDefaultSportPage($sportName),
              'path' => ['alias' => '/' . strtolower($sportName)],
              'body' => [
                'value' => $getInfoObj->getDefaultSportPage($sportName),
                'summary' => 'Live stream ' . $sportName . ' | Online ' . $sportName . ' i dag ',
                'format' => 'full_html',
              ],
            ];
            $newNode = $this->createNodeGeneric($node);
            $taxonomySportId = $this->defaultTournamentTaxonomy('',$sportName,'sport',$sportid,'null',$sportid);

            $this->createItemMenu('main', '', $newNode, $sportName,$taxonomySportId,$sport_id);
            $this->createSportPages($sport_id);
            echo 'Creating Sport Pages -' . $sportName . ' - ' . "\n";
        } else {
            $competition = $rpClient->getSportbyID($parameters);
            $competitionName = $competition["name"];
            $taxonomyObj = $getInfoObj->getTaxonomy($competitionName);
            $taxonomySportId = reset($taxonomyObj)->id();
            echo 'Using Sport - ' . $competitionName . ' - ' . "\n";
        }
        return $taxonomySportId;
    }
    public function createSportInternPages($sportTags, $sportName, $name, $id,$url,$sportApiId, $type)
    {
    $getInfoObj = new RepoGeneralGetInfo();
    if ($type) {
      $createStreamPages = [
        'type' => $type,
        'title' => $name,
        'field_sport_tax' => $sportTags,
        'field_page_type' => $id,
        'field_sportip_api_id' => $sportApiId,
        'body' => [
          'value' => '',
          'summary' => '',
          'format' => 'full_html',
        ],
        'path' => ['alias' => '/' . $getInfoObj->getClearUrl($sportName) . '/' . $getInfoObj->getClearUrl($url)],
      ];
      $id = $this->createNodeGeneric($createStreamPages);
      echo 'Creating Sport Intern Pages - ' . $name . ' - ' . "\n";
    }
    return $id;

  }
    public function createSportInternBlogs($sportTags, $sportName, $name, $id,$url,$sportApiId, $type)
    {
      $getInfoObj = new RepoGeneralGetInfo();
      if ($type) {
        $createStreamPages = [
          'type' => $type,
          'title' => $name,
          'field_sport_blog_taxonomy' => $sportTags,
          'field_sport_blog_ip_api_id' => $sportApiId,
          'body' => [
            'value' => '',
            'summary' => '',
            'format' => 'full_html',
          ],
          'path' => ['alias' => '/' . $getInfoObj->getClearUrl($sportName) . '/' . $getInfoObj->getClearUrl($url)],
        ];
        $id = $this->createNodeGeneric($createStreamPages);
        echo 'Creating Sport Internal Blog Page - ' . $name . ' - ' . "\n";
      }
      return $id;

    }

    public function createStreamPages($streams, $sport_tags)
    {
        $i = 0;
        $tags_array = [];
        $getInfoObj = new RepoGeneralGetInfo();
        foreach ($streams as $stream) {
            $node = $getInfoObj->getNode($stream['id'],'stream_provider', 'field_api_str_id');
            $rpClient = RPAPIClient::getClient();
            $parameters = ['id' => $stream['id']];
            $streamObj = $rpClient->getStreamprovidersbyID($parameters);
            $parameters1 = ['id' => $streamObj['type']];
            $streamType = $rpClient->getStreamproviderTypesbyID($parameters1);

            if (!$node) {
                $term = $this->createTaxonomy($streamObj['name'],'stream_provider');
                $taxonomyId = $term->id();
                $tags_array [$i] = ['target_id' => $taxonomyId];
                $createStreamPages = [
                  'type' => 'stream_provider',
                  'title' => $streamObj['name'],
                  'field_general_label' => $streamObj['name'],
                  'field_api_str_id' => $streamObj['id'],
                  'field_stream_provider' => $taxonomyId,
                  'field_tags' => $sport_tags,
                  'field_stream_provider_type' => $streamType['name'],
                  'field_stream_provider_type_api_i' => $streamType['id'],
                ];
                $this->createNodeGeneric($createStreamPages);
                echo 'Creating Stream Provider Pages -' . $streamObj['name'] . ' - ' . "\n";
            }
            else {
                $data = reset($node);
                $node = NODE::load($data->id());
                $name = $data->title->value;
                if($name != $streamObj['name']){
                    $term = $this->createTaxonomy($name, 'stream_provider');
                    $taxonomyId = $term->id();
                    $tags_array [$i] = ['target_id' => $taxonomyId];
                    echo 'Update Stream Provider Pages -' . $name . ' - ' . "\n";
                }
                else{
                  $term = $this->createTaxonomy($streamObj['name'],'stream_provider');
                  $taxonomyId = $term->id();
                  $tags_array [$i] = ['target_id' => $taxonomyId];
                }
            }
            $i++;
        }
        return $tags_array;
    }
    public function createTournamentPages($competition, $sport_tags, $sportName)
    {
        $type = 'tournament_page';
        $competitionId = $competition['id'];
        $opc = 'field_tournament_api_id';
        $getInfoObj = new RepoGeneralGetInfo();
        $node = $getInfoObj->getNode($competitionId,$type, $opc);
        if (empty($node)) {
            $rpClient = RPAPIClient::getClient();
            $parameters = ['id' => $competitionId];
            $competition = $rpClient->getCompetitionsbyID($parameters);
            $competitionName = $competition[0]["name"];
            $competitionIdApi = $competition[0]["id"];
            $logo_path = $competition[0]["logo_path"];
            $logo_modified = $competition[0]["logo_modified"];
            $logo = $getInfoObj->getImg($logo_path,$getInfoObj->getClearUrl($competitionName . '_logo'));
            $competition_array[0] = $competition;
            $NewTax = $this->createTournametTaxonomy($competition_array,'sport', $sport_tags, $sportName);
            if ($NewTax) {
                $createTournamentPages = [
                  'type' => $type,
                  'title' => $competitionName,
                  'field_tournament_head' => $competitionName,
                  'field_tournament_reff' => $NewTax,
                  'field_tournament_subheading' => 'Se ' . $competitionName . ' med os',
                  'field_live_stream_button' => 'Se ' . $competitionName . ' med os',
                  'field_tournament_api_id' => $competitionIdApi,
                  'field_stream_provider' => $competition,
                  'field_logo' => $logo,
                  'field_logo_date' => $logo_modified,
                  'status' => false,
                  'field_tournament_sport_taxonomy' => $sport_tags,
                  'body' => [
                    'value' => $getInfoObj->getDefaultTeam($competitionName),
                    'summary' => '',
                    'format' => 'full_html',
                  ],
                ];
                $node = $this->createNodeGeneric($createTournamentPages);
                echo 'Creating Tournament -' . $competitionName . ' - ' . "\n";
            }
        }
        else{
            $node = reset($node)->id();
            $updateObj = new UpdateClienetData();
            $updateObj->updateTournament($competitionId);
        }
        return $node;

    }
    public function createChannelsPages()
    {
        $rpClient = RPAPIClient::getClient();
        $AllChannel = $rpClient->getChannel();
        $getInfoObj = new RepoGeneralGetInfo();
        foreach ($AllChannel["results"] as $channel) {
            $oldChanel = $getInfoObj->getNode($channel['id'],'channels', 'field_channel_api_id');
            if (empty($oldChanel)) {
                $newChanel = [
                  'type' => 'channels',
                  'title' => $channel['name'],
                  'field_channel_api_id' => $channel['id'],
                  'field_channel_name' => $channel['name'],
                  'field_channel_code' => $channel['name'],
                  'field_channel_description‎' => $channel['description'],
                  'field_channel_notes' => $channel['notes'],
                ];
                $this->createNodeGeneric($newChanel);
                echo 'Creating node (Channels) "' . $channel['name'] . "\n";
            } else {
                $nodeId = reset($oldChanel)->id();
                $updateObj = new UpdateClienetData();
                $updateObj->updateChanel($nodeId,$oldChanel);
            }
        }
        return true;
    }
    public function createGamePage($sport_tags,$schedule,$stream = '',$defautltText,$Tags_Team,$ChannelByNode,$parameters) {
        $getInfoObj = new RepoGeneralGetInfo();
        $tournament = $getInfoObj->getTaxonomyByAPIID($schedule['competition']['id']);

        $sportId = $getInfoObj->getNode($schedule["sport"]["id"],'sport_pages', 'field_sport_api_id');
        $sportName = reset($sportId)->gettitle();
        $node = [
          'type' => 'game_pages',
          'field_game_pages_api_id' => $schedule['id'],
          'field_tags' => $sport_tags,
          'field_game_participants_tax' => $Tags_Team,
          'field_game_tournament_reff' => $tournament->id(),
          'title' => $schedule['name'],
          'field_game_date' => strtotime($schedule['start']),
          'field_participant_1' => $schedule['participants'][0]['name'],
          'field_participant_2' => $schedule['participants'][1]['name'],
          'field_game_tournament' => $tournament->title->value,
          'field_game_tournament_api_id' => $schedule['competition']['id'],
          'body' => [
            'value' => $defautltText,
            'summary' => $defautltText,
            'format' => 'full_html',
          ],
          'field_stream_provider_gp' => $stream,
          'field_game_channels_ref' => $ChannelByNode,
          'path' => ['alias' => '/' . $getInfoObj->getClearUrl($sportName) . '/' . $getInfoObj->getClearUrl($schedule['name'])],
        ];
        $this->createNodeGeneric($node);
        echo 'Creating Game Page - ' . $schedule['name'] . ' -' . "\n";
        echo "\n";
        return $node;
    }
    public function createParticipantPages($partArray, $sport_tags)
    {
        $tags_team_array = [];
        $getInfoObj = new RepoGeneralGetInfo();
        foreach ($partArray as $team) {
            $name = $team['name'];
            $type = 'team_content';
            $opc = 'field_team_api_id';
            $node = $getInfoObj->getNode($name, $type,$opc);
            if (!$node) {
                $term = $this->createParticipantTaxonomy($name, 'participant',$team ['id']);
                $team_tax = $term->id();
                $tags_team_array [] = ['target_id' => $team_tax];
                $parameters = ['id' => $team ['id']];
                $rpClient = RPAPIClient::getClient();
                $Participants = $rpClient->getParticipantsbyID($parameters);
                $logo_path = $Participants[0]["logo_path"];
                $logo_modified = $Participants[0]["logo_modified"];
                $logo = $getInfoObj->getImg($logo_path,$getInfoObj->getClearUrl($name . '_logo'));
                $node = [
                  'type' => 'team_content',
                  'status' => false,
                  'field_team_tax' => $team_tax,
                  'field_away_article_title' => $team ['name'],
                  'title' => $team ['name'],
                  'field_team_api_id' => $team ['id'],
                  'field_live_stream_button_' => $team ['name'],
                  'field_page_teams_header' => $team ['name'],
                  'field_stream_providers_teams' => $team ['name'],
                  'field_team_sport_taxonomy' => $sport_tags,
                  'field_participant_logo' => $logo,
                  'field_logo_participant_date' => $logo_modified,
                  'body' => [
                    'value' => $getInfoObj->getDefaultTeam($team ['name']),
                    'summary' => '',
                    'format' => 'full_html',
                  ],
                ];
                $this->createNodeGeneric($node);
                echo 'Creating Participant - ' . $team ['name'] . ' - ' . "\n";
            }
            else{
                $node = reset($node)->id();
                $updateObj = new UpdateClienetData();
                $updateObj->updateParticipant($node);
            }
        }
        return $tags_team_array;
    }

    //---------------------------------------------------------- ENTITY "META" CREATION ----------------------------

    public function createMeta($event)
    {

        //General Variable
        $eventID = $event['id'];

        // Sport Variable
        $metaID = $event['sport']['id'];
        $metaSportArray = $event['sport'];
        $metaName = 'Sport - Event Id/' . $eventID;
        $this->createSimpleMeta($metaID, $eventID, $metaSportArray, $metaName);

        // Competition Variable
        $metaID = $event['competition']['id'];
        $metaCompetitionArray = $event['competition'];
        $metaName = 'Competition - Event Id/' . $eventID;
        $this->createSimpleMeta($metaID, $eventID, $metaCompetitionArray, $metaName);

        // Event Variable
        $metaID = $event['id'];
        $metaEventArray = $event;
        $metaName = 'Event - Event Id/' . $eventID;
        $this->createSimpleMeta($metaID, $eventID, $metaEventArray, $metaName);

        //---------------------------------------------------

        // Participants Variable
        $metaParticipantsArray = $event['participants'];
        $metaName = 'Participants - Event Id/' . $eventID;
        $this->createMultipleMeta($eventID, $metaParticipantsArray, $metaName);

        // Streamprovider Variable
        $metaStreamproviderArray = $event['streamprovider'];
        $metaName = 'Stream Provider - Event Id/' . $eventID;
        $this->createMultipleMeta($eventID, $metaStreamproviderArray,
          $metaName);

        return true;
    }
    public function createSimpleMeta($metaID, $eventID, $metaSportArray, $metaName)
    {
        foreach ($metaSportArray["meta"] as $array) {
            $data = [
              'name' => $metaName,
              'field_cannel_apiid' => $array['channel'],
              'field_order_priority' => $array['priority'],
              'field_events_apiid' => $eventID,
              'field_meta_type_event_sport_part' => $metaName,
              'field_meta_type_api_id_event_id' => $metaID,
            ];
            $node = \Drupal::entityTypeManager()
              ->getStorage('channels_by_contenttype')
              ->create($data);
            $node->save();
        }
    }
    public function createMultipleMeta($eventID, $metaDescriptionArray, $metaName)
    {
        foreach ($metaDescriptionArray as $metaArray) {
          var_dump($metaArray);
            foreach ($metaArray["meta"] as $array) {
                $data = [
                  'name' => $metaName,
                  'field_cannel_apiid' => $array['channel'],
                  'field_order_priority' => $metaArray['running_order'],
                  'field_events_apiid' => $eventID,
                  'field_meta_type_event_sport_part' => $metaName,
                  'field_meta_type_api_id_event_id' => $metaArray['id'],
                ];
                $node = \Drupal::entityTypeManager()
                  ->getStorage('channels_by_contenttype')
                  ->create($data);
                $node->save();
            }
        }
    }

    //-------------------------------------------------------- TAXONOMY CREATION ----------------------------

    public function createTaxonomy($name, $voc)
    {
        $getInfoObj = new RepoGeneralGetInfo();
        $taxonomy = $getInfoObj->getTaxonomy($name);
        if (!$taxonomy) {
            $term = Term::create([
              'parent' => [],
              'name' => $name,
              'vid' => $voc,
            ]);
            $term->save();
            $taxonomy = $getInfoObj->getTaxonomy($name);
        }
        $term = reset($taxonomy);
        return $term;

    }
    public function createParticipantTaxonomy($name, $voc, $idApi)
    {
        $getInfoObj = new RepoGeneralGetInfo();
        $taxonomy = $getInfoObj->getTaxonomy($name);
        if (!$taxonomy) {
            $term = Term::create([
              'parent' => [],
              'name' => $name,
              'vid' => $voc,
              'field_participant_api_id' => $idApi,
            ]);
            $term->save();
            $taxonomy = $getInfoObj->getTaxonomy($name);
        }
        $term = reset($taxonomy);
        return $term;

    }
    public function createTournametTaxonomy($competition,$voc,$sport_tags,$sport_id) {
        $getInfoObj = new RepoGeneralGetInfo();
        $index = count($competition) - 1;
        $tournamentId = $competition[0][0]["id"];
        $tournamentName = $competition[0][0]["name"];
        $tournamentParent = $competition[0][0]["parent"];
        $taxonomy = $getInfoObj->getTaxonomyByAPIID($tournamentId);
        if (!$taxonomy) {
            if ($tournamentParent == null) {
                $this->defaultTournamentTaxonomy($sport_tags, $tournamentName,$voc, $tournamentId, 'Sport id: ' . $sport_id, $sport_id);
            } else {
                $competition = $getInfoObj->getTaxonomyParent($competition);
                $index = count($competition) - 1;
                for ($i = $index; $i >= 0; $i--) {
                    $id = $competition[$i][0]["id"];
                    $name = $competition[$i][0]["name"];
                    $tournamentId = $competition[$i][0]["id"];
                    $parent_id = $competition[$i][0]["parent"];
                    $parentTaxonomyId = $competition[$i + 1][0]["id"];
                    $taxonomy = $getInfoObj->getTaxonomyByAPIID($id);
                    if (empty($taxonomy)) {
                        if ($parent_id != null) {
                            $parentTaxonomyId = $getInfoObj->getTaxonomyByAPIID($parentTaxonomyId);
                            $parentTaxonomyId = $parentTaxonomyId->id();
                            $this->defaultTournamentTaxonomy($parentTaxonomyId,$name, $voc, $tournamentId, $parent_id,$sport_id);
                        } else {
                            $this->defaultTournamentTaxonomy($sport_tags, $name,$voc, $tournamentId, 'Sport id: ' . $sport_id,$sport_id);
                        }
                    }

                }

            }
        }
        $tournamentID = $competition[0][0]["id"];
        $tournamentTaxonomy = $getInfoObj->getTaxonomyByAPIID($tournamentID);
        return $tournamentTaxonomy->id();
    }

    public function defaultTournamentTaxonomy($parentId,$name,$voc,$tournamentId,$tournamentParents,$sport_id){
        $term = Term::create([
          'parent' => [$parentId],
          'name' => $name,
          'vid' => $voc,
          'field_api_id' => $tournamentId,
          'field_api_parent' => $tournamentParents,
          'field_sport_api_id' => $sport_id,
        ]);
        $term->save();
        $getInfoObj = new RepoGeneralGetInfo();
        $taxonomy = $getInfoObj->getTaxonomyByAPIID($tournamentId);
        return $taxonomy->id();

    }
    public function createVocabulary()
    {
        $vocabularys = [
          'sport',
          'stream_provider',
          'participant',
          'tournament',
        ];
        foreach ($vocabularys as $vocal) {
            $vocabulary = \Drupal\taxonomy\Entity\Vocabulary::create([
              'vid' => $vocal,
              'description' => '',
              'name' => $vocal,
            ]);
            $vocabulary->save();
        }
        return true;

    }

    //-------------------------------------------------------- Generic CREATION ----------------------------

    public function createNodeGeneric($data)
    {
        $node = Node::create($data);
        $node->save();
        return $node->id();
    }
    public function createItemMenu($menu_name,$description,$node,$sport,$sport_tags,$sport_id) {
        $id_node = \Drupal::entityTypeManager()->getStorage('menu')->loadByProperties(['id' => $menu_name]);
        if (!$id_node) {
            $menu = \Drupal::entityTypeManager()->getStorage('menu')->create([
                'id' => $menu_name,
                'label' => $menu_name,
                'menu_name' => $menu_name,
                'description' => $description,
              ])->save();
            echo "Creating Menu $menu_name" . "\n";
        }

        $menu_link = \Drupal::entityTypeManager()->getStorage('menu_link_content')->loadByProperties(['menu_name' => $menu_name, 'title' => $sport]);

        if (empty($menu_link)) {
            $menu_link_Sport = MenuLinkContent::create([
              'title' => $sport,
              'link' => ['uri' => 'internal:/node/' . $node],
              'menu_name' => $menu_name,
              'expanded' => true,
            ])->save();

            $menu_link_SportObj = \Drupal::entityTypeManager()
              ->getStorage('menu_link_content')
              ->loadByProperties([
                'menu_name' => $menu_name,
                'title' => $sport,
              ]);
            $uuid = reset($menu_link_SportObj)->uuid->value;
            if ($uuid) {
                $forside = $sport . ' Forside';
                $LiveStream = 'Live Stream ' . $sport;
                $url = 'Live Stream ' . $sport;
                $id = 'liveStream';
                $LiveStream_id = $this->createSportInternPages($sport_tags,$sport, $LiveStream, $id,$url,$sport_id, 'sport_internal_pages');

                $Blog = 'Blog '.$sport;
                $url = 'Blog';
                $id = 'blog';
                $Blog_id = $this->createSportInternblogs($sport_tags, $sport,$Blog, $id,$url,$sport_id,'sport_internal_blogs');


                $menu_link_Sport_intern = MenuLinkContent::create([
                  'title' => $forside,
                  'link' => ['uri' => 'internal:/node/' . $node],
                  'menu_name' => 'main',
                  'expanded' => true,
                  'parent' => 'menu_link_content:' . $uuid,
                  'weight' => 0,
                ])->save();
                $menu_link_Sport_intern = MenuLinkContent::create([
                  'title' => $LiveStream,
                  'link' => ['uri' => 'internal:/node/' . $LiveStream_id],
                  'menu_name' => 'main',
                  'expanded' => true,
                  'parent' => 'menu_link_content:' . $uuid,
                  'weight' => 1,
                ])->save();
                $menu_link_Sport_intern = MenuLinkContent::create([
                  'title' => 'Blog',
                  'link' => ['uri' => 'internal:/node/' . $Blog_id],
                  'menu_name' => 'main',
                  'expanded' => true,
                  'parent' => 'menu_link_content:' . $uuid,
                  'weight' => 2,
                ])->save();
            }
            echo "Creating menu item $sport on Menu $menu_name" . "\n";
        }
        return true;
    }
    public function createMenu($menu_name, $description, $node, $sport)
    {
        for ($i = 0; $i < count($menu_name); $i++) {
            $menuid = $menu_name[$i] . '_id';
            $id_node = \Drupal::entityTypeManager()
              ->getStorage('menu')
              ->loadByProperties(['id' => $menuid]);
            if (!$id_node) {
                $menu = \Drupal::entityTypeManager()->getStorage('menu')
                  ->create([
                    'id' => $menuid,
                    'label' => $menu_name[$i],
                    'menu_name' => $menu_name[$i],
                    'description' => $description[$i],
                  ])->save();
                echo "Creating Menu $menu_name[$i]" . "\n";
            }

            $menu_link = \Drupal::entityTypeManager()
              ->getStorage('menu_link_content')
              ->loadByProperties(['menu_name' => $menuid, 'title' => $sport]);

            if (empty($menu_link)) {
                $menu_link = MenuLinkContent::create([
                  'title' => $sport,
                  'link' => ['uri' => 'internal:/node/' . $node],
                  'menu_name' => $menuid,
                  'expanded' => true,
                ])->save();
                echo "Creating menu item $sport on Menu $menu_name[$i]" . "\n";
            }
        }
        return true;
    }
    public function createSportPages_multiplesMenu($sport_id)
    {
        $rpClient = RPAPIClient::getClient();
        $para = ['id' => $sport_id];
        $getInfoObj = new RepoGeneralGetInfo();
        $node_id = $getInfoObj->getNode($sport_id,'sport_pages', 'field_sport_api_id');
        if (empty($node_id)) {
            $competition = $rpClient->getSportbyID($para);
            $name = $competition["name"];
            $id_api = $competition["id"];
            $node = [
              'type' => 'sport_pages',
              'title' => $name,
              'field_sport_name' => $name,
              'field_sport_api_id' => $id_api,
              'field_sport_theme_descrption' => $getInfoObj->getDefaultSportPage($name),
              'path' => ['alias' => '/' . strtolower($name)],
              'body' => [
                'value' => $getInfoObj->getDefaultSportPage($name),
                'summary' => 'Live stream ' . $name . ' | Online ' . $name . ' i dag ',
                'format' => 'full_html',
              ],
            ];
            $new_node = $this->createNodeGeneric($node);

            $menu_name = [
              'TopSportMenu',
              'FrontTopSportMenu',
              'FrontButtomSportMenu',
            ];
            $description = [
              'General Top Sport Menu',
              'Front Page Top Sport Menu',
              'Front Page Buttom Sport Menu',
            ];

            $term = $this->createTaxonomy($name, 'sport');
            $sport_tags = $term->id();
            $this->createMenu($menu_name, $description, $new_node, $name);
            $this->createSportPages($sport_id);
            echo 'Creating Sport Pages -' . $name . ' - ' . "\n";
        } else {
            $competition = $rpClient->getSportbyID($para);
            $name_sport = $competition["name"];
            $term = $getInfoObj->getTaxonomy($name_sport);
            $sport_tags = reset($term)->id();
            echo 'Get Sport Pages Taxonomy-' . $name_sport . ' - ' . "\n";
        }
        return $sport_tags;
    }
    public function CreateClientData(){}

}
