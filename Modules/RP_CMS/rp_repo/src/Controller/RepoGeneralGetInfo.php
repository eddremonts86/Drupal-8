<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 6/19/17
 * Time: 10:34 AM
 */

namespace Drupal\rp_repo\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Client;
use Drupal\rp_api\RPAPIClient;
use Drupal\Core\Url;
use Drupal\rp_cms_site_info\Utility\RPCmsSiteInfoHelper;

/**
 * Class ImportAPIDATA.
 *
 * @package Drupal\rp_repo\Controller
 */
class RepoGeneralGetInfo extends ControllerBase {

  public function getConfig($date = 'Y-m-d', $days = 7) {
    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api_id = $config->get('rp_base_site_api_id');
    $data = RPCmsSiteInfoHelper::getSiteInfoCombos();
    $date = date($date);
    $site = $site_api_id;
    if ($site_api_id or $date) {
      foreach ($data as $siteInfo) {
        $paramList [] = [
          'site' => $site,
          'lang' => $siteInfo[1],
          'region' => $siteInfo[0],
          'start' => $date,
          'days' => $days,
          'tz' => date_default_timezone_get(),
        ];
      }
      return $paramList;
    }
    else {
      echo "Please make a basic configuration of the site \n";;
      return FALSE;
    }

  }

  public function getRepoData($url) {
    $client = new Client();
    $response = $client->request('GET', $url);
    $data = json_decode($response->getBody()->getContents());
    return $data;
  }

  public function getSiteByID($siteID) {
    $site = \Drupal::entityTypeManager()
      ->getStorage('site')
      ->loadByProperties(['field_site_api_id' => $siteID]);
    return $site;
  }

  public function getTaxonomy($name) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $name]);
    return $taxonomy;
  }

  public function getTaxonomyByID($entityId) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['tid' => $entityId]);
    return reset($taxonomy);
  }

  public function getTaxonomyByAPIID($id) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['field_api_id' => $id]);
    return reset($taxonomy);
  }

  public function getTaxonomyByCriterio($id, $feald) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties([$feald => $id]);
    return reset($taxonomy);
  }

  public function getTaxonomyByCriterioMultiple($obj, $reset = 0) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties($obj);
    if($reset != 0 ){return $taxonomy;}
    else{return reset($taxonomy);}


  }

  public function getTaxonomyByParticipantAPIID($id) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['field_participant_api_id' => $id]);
    return reset($taxonomy);
  }

  public function getNode($name, $type, $opc) {
    $id_node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['type' => $type, $opc => $name]);
    return $id_node;
  }

  public function getDefaultText($part1, $part2) {
    /* $data = "
 <h2>Se $part1 vs $part2 online. </h2>

   <p>Fodbold skal ses, høres og mærkes. Med streaming får du alle friheder
   til at se yndlingskampen på lige præcis din måde.<br>
   Du er ikke bundet af din fladskærm i stuen, men kan nu følge favoritholdet
   på din tablet, computer eller telefon lige nøjagtigt, hvor du vil. Se kampene
   i sommerhuset, i bussen eller på en fortovscafé med en iskold øl i hånden.<br>
   Du bestemmer helt selv og med et kæmpe udvalg af kampe er der altid en
   begivenhed til lige netop dig.</p>

 <h3>Sådan streamer du fodbold</h3>

   <p>Læs vores guide til streaming og find den udbyder, der passer dig bedst.
   Vi anbefaler kun lovlig streaming i den bedste kvalitet, og der er nok at
   vælge imellem.<br>
   Vil du gerne se kampe og samtidig åbne en spillekonto? Foretrækker du bare
   et kæmpe udvalg af kampe? Eller vil du følge en bestemt liga eller et bestemt
   hold? Mulighederne er mange, og vi hjælper dig til at træffe det bedste valg
   til dit behov.</p>";*/
    $data = "";
    return $data;
  }

  public function getDefaultTeam($part) {
    /*  $data = "
<h1><b>$part</b></h1>

  <p>Fodbold skal ses, høres og mærkes. Med streaming får du alle friheder
  til at se yndlingskampen på lige præcis din måde.<br>
  Du er ikke bundet af din fladskærm i stuen, men kan nu følge favoritholdet
  på din tablet, computer eller telefon lige nøjagtigt, hvor du vil. Se kampene
  i sommerhuset, i bussen eller på en fortovscafé med en iskold øl i hånden.<br>
  Du bestemmer helt selv og med et kæmpe udvalg af kampe er der altid en
  begivenhed til lige netop dig.</p>

<h3>Sådan streamer du fodbold</h3>

  <p>Læs vores guide til streaming og find den udbyder, der passer dig bedst.
  Vi anbefaler kun lovlig streaming i den bedste kvalitet, og der er nok at
  vælge imellem.<br>
  Vil du gerne se kampe og samtidig åbne en spillekonto? Foretrækker du bare
  et kæmpe udvalg af kampe? Eller vil du følge en bestemt liga eller et bestemt
  hold? Mulighederne er mange, og vi hjælper dig til at træffe det bedste valg
  til dit behov.</p>";*/
    $data = "";
    return $data;
  }

  public function getDefaultSportPage($sport) {
    /*$data = "[page-header type='main' title='Se::Live Stream::'.$sport.'' background_image='themes/custom/steve/images/fodbold-desktop-banner.jpg']
             [match-ad type='page_header_main' title=''.$sport.' skal ses live!' subtitle='Følg dit yndlingshold' modes_1_title='live på TV' modes_1_icon_class='glyphicon-home' modes_2_title='live på stadion' modes_2_icon_class='glyphicon-plane' modes_3_title='eller live online' modes_3_subtitle='på pc, mobil eller tablet.' modes_3_icon_class='glyphicon-play-circle']
             [/match-ad]
             [/page-header]
             [matches-banner count='3' title='De mest populære kampe til DIG' subtitle='Vi giver dig den bedste '.$sport.' i verden, de fedeste optakter og muligheden for at se live stream '.$sport.' i den højeste kvalitet. Hver dag bliver en '.$sport.'-fest!' background_image='themes/custom/steve/images/bg-matches-banner.png']
             [/matches-banner]
             [schedule title='Kampprogram' background_image='themes/custom/steve/images/ground-image-football.jpg']
             [/schedule]
             [stream-providers title='Find din live stream udbyder']
             [/stream-providers]";
    $data = "
  [page-header type='main' title='Se::Live Stream::$sport' background_image='themes/custom/steve/images/fodbold-desktop-banner.jpg']
  [match-ad type='page_header_main' title='$sport skal ses live!'subtitle='Følg dit yndlingshold' modes_1_title='live på TV' modes_1_icon_class='glyphicon-home' modes_2_title='live på stadion' modes_2_icon_class='glyphicon-plane' modes_3_title='eller live online' modes_3_subtitle='på pc, mobil eller tablet.' modes_3_icon_class='glyphicon-play-circle'][/match-ad][/page-header]
  [matches-banner count='3' title='De mest populære kampe til DIG' subtitle='Vi giver dig den bedste $sport i verden, de fedeste optakter og muligheden for at se live stream $sport i den højeste kvalitet. Hver dag bliver en fodbold-fest!' background_image='themes/custom/steve/images/bg-matches-banner.png'][/matches-banner]
  [schedule title='Kampprogram' background_image='themes/custom/steve/images/ground-image-football.jpg'][/schedule]
  [stream-providers title='Find din live stream udbyder'][/stream-providers]";*/
    $data = '';
    return $data;
  }

  public function getDefaultSportInternalPage($id, $name, $sport_name) {
    $data = [
      2 => "
               [page-header type='compact' background_image='themes/custom/steve/images/content_bg_generic.png' title='Live stream $sport_name | $sport_name i TV | Live stream providere' p1='Vi har samlet de største udbydere af live $sport_name, som viser kampe fra de store internationale og europæiske ligaer, landskampe og meget mere...' p2='Udbyderne har forskellige rettigheder til ligaerne. Se hvilke ligaer de forskellige udbydere viser kampe fra nedenfor.']
               [carousel type='matches' count='3' background_image='themes/custom/steve/images/ground-small.png' title='Udvalgte kampe'][/carousel]
               [/page-header]
               [stream-providers type='list' size='large' title='Find din live stream udbyder' background_image='themes/custom/steve/images/ground-image-football.jpg'][/stream-providers]",
      3 => "Blog Contents",
      4 => "",
    ];
    return $data [4];
  }

  public function getIdChannelByNode($nodList) {
    $tags_array = [];
    foreach ($nodList as $id) {
      $ischanel = self::getTaxonomyByCriterio($id, 'field_channel_api_id');
      $tags_array [] = ['target_id' => $ischanel->id()];
    }
    return $tags_array;
  }

  public function getDataApi($url) {
    $base = 'http://steve.rebelpenguin.dk:10080/api/';
    $format = '/?format=json';
    $client = \Drupal::httpClient();
    $comp = $client->request('GET', $base . $url . $format);
    $response = json_decode($comp->getBody()->getContents());
    return $response;
  }

  public function getTaxonomyParent($competition) {
    $rpClient = RPAPIClient::getClient();
    $index = count($competition) - 1;
    $tournamentParent = $competition[$index][0]["parent"];
    $parameters = ['id' => $tournamentParent];
    if ($tournamentParent != NULL) {
      $newCompetition = $rpClient->getCompetitionsbyID($parameters);
      $competition[count($competition)] = $newCompetition;
    }
    else {
      $newCompetition = $rpClient->getCompetitionsbyID($parameters);
      $competition[count($competition)] = $newCompetition;
    }
    return $competition;
  }

  public function getImg($url, $alias) {
    $data = file_get_contents($url);

    if (isset($data)) {
      $file = file_save_data($data, "public://" . $alias . ".png", FILE_EXISTS_REPLACE);
    }
    else {
      $url = "http://steve.rebelpenguin.dk:10080/media/participants/default.png";
      $data = file_get_contents($url);
      $file = file_save_data($data, "public://" . $alias . ".png", FILE_EXISTS_REPLACE);
    }

    $data_img = [
      'target_id' => $file->id(),
      'alt' => $this->getClearUrl($alias),
      'title' => $this->getClearUrl($alias),
    ];
    return $data_img;
  }

  public function getClearUrl($s) {
    $s = trim($s, "\t\n\r\0\x0B");
    //--- Latin ---//
    $s = str_replace('ü', 'u', $s);
    $s = str_replace('Á', 'A', $s);
    $s = str_replace('á', 'a', $s);
    $s = str_replace('é', 'e', $s);
    $s = str_replace('É', 'E', $s);
    $s = str_replace('í', 'i', $s);
    $s = str_replace('Í', 'I', $s);
    $s = str_replace('ó', 'o', $s);
    $s = str_replace('Ó', 'O', $s);
    $s = str_replace('Ú', 'U', $s);
    $s = str_replace('ú', 'u', $s);
    //--- Nordick ---//
    $s = str_replace('ø', 'o', $s);
    $s = str_replace('Ø', 'O', $s);
    $s = str_replace('Æ', 'E', $s);
    $s = str_replace('æ', 'e', $s);
    $s = str_replace('Å', 'A', $s);
    $s = str_replace('å', 'a', $s);
    //--- Others ---//

    $s = str_replace(' - ', '-vs-', $s);
    $s = str_replace(' ', '_', $s);
    $s = str_replace('.', '_', $s);
    $s = str_replace('\"', '_', $s);
    $s = str_replace(':', '_', $s);
    $s = str_replace(',', '_', $s);
    $s = str_replace(';', '_', $s);
    $s = str_replace('/', '_', $s);
    $s = strtolower($s);
    $s = trim($s, "\t\n\r\0\x0B");
    return $s;
  }

  public function getMultiplesAlias($urldata) {
    $data = RPCmsSiteInfoHelper::getSiteInfoCombos();
    foreach ($data as $siteInfo) {
      $alia = '/' . $this->getClearUrl(strtolower($siteInfo[0])) . '/' . $urldata;
      $ifAlias = $this->getAliasbyUrl($alia);
      if (empty($ifAlias)) {
        $list [] = ['alias' => $alia];
      }
    }
    return $list;
  }

  public function getAlias($nid, $alias) {
    $query_selct = \Drupal::database()->select('url_alias', 'url');
    $query_selct->fields('url', ['pid', 'alias', 'source']);
    $query_selct->condition('url.source', $nid, '=');
    $query_selct->condition('url.alias', $alias, '=');
    $data = $query_selct->execute()->fetchAll();
    return $data;
  }

  public function getAliasbyUrl($alias) {
    $query_selct = \Drupal::database()->select('url_alias', 'url');
    $query_selct->fields('url', ['pid', 'alias', 'source']);
    $query_selct->condition('url.alias', $alias, '=');
    $data = $query_selct->execute()->fetchAll();
    return $data;
  }


}