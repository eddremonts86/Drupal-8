<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 4:05 PM
 */

namespace Drupal\rp_repo\Controller\entities\Generales;

use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\rp_repo\Controller\entities\Pages\pages;
use Drupal\rp_repo\Controller\entities\Pages\sportPages;
use Drupal\rp_repo\Controller\entities\Pages\sportBlogPages;


class menu
{
    /**
     * function createChannelsPages ( )
     * use't
     */
    public function createSportPages_multiplesMenu($sport_id)
    {
        $rpClient = RPAPIClient::getClient();
        $para = ['id' => $sport_id];
        $getInfoObj = new RepoGeneralGetInfo();
        $node_id = $getInfoObj->getNode($sport_id, 'sport_pages', 'field_sport_api_id');
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
                'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => 'Site')))->id(),
                'body' => [
                    'value' => $getInfoObj->getDefaultSportPage($name),
                    'summary' => 'Live stream ' . $name . ' | Online ' . $name . ' i dag ',
                    'format ' => 'full_html',
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
            print ' Creating Sport Pages -' . $name . '- at ' . date("h:i:s") . "\n";
        } else {
            $competition = $rpClient->getSportbyID($para);
            $name_sport = $competition["name"];
            $term = $getInfoObj->getTaxonomy($name_sport);
            $sport_tags = reset($term)->id();
            print 'Get Sport Pages Taxonomy-' . $name_sport . '- at ' . date("h:i:s") . "\n";
        }
        return $sport_tags;
    }

    /**
     * function createMenu ( )
     * Creating menu items
     * Parametre
     * Menu name , a small description, node id , sport name
     * return a Boolean
     *
     */
    public function createMenu($menu_name, $description, $nodeid, $sport)
    {
        for ($i = 0; $i < count($menu_name); $i++) {
            $menuid = $menu_name[$i] . '_id';
            $id_node = \Drupal::entityTypeManager()->getStorage('menu')->loadByProperties(['id' => $menuid]);
            if (!$id_node) {
                $menu = \Drupal::entityTypeManager()->getStorage('menu')->create([
                    'id' => $menuid,
                    'label' => $menu_name[$i],
                    'menu_name' => $menu_name[$i],
                    'description' => $description[$i],
                ])->save();
                print " Creating Menu $menu_name[$i]" . ' - at ' . date("h:i:s") . "\n";
            }

            $menu_link = \Drupal::entityTypeManager()->getStorage('menu_link_content')->loadByProperties([
                'menu_name' => $menuid, 'title' => $sport
            ]);

            if (empty($menu_link)) {
                $menu_link = MenuLinkContent::create([
                    'title' => $sport,
                    'link' => ['uri' => 'internal:/node/' . $nodeid],
                    'menu_name' => $menuid,
                    'expanded' => TRUE,
                ])->save();
                print " Creating menu item $sport on Menu $menu_name[$i]" . ' - at ' . date("h:i:s") . "\n";
            }
        }
        return TRUE;
    }

    /**
     * function createItemMenu ( )
     * Parametre :
     * Menu name , small desc, Node id , sport name, sport taxonomy id , region(ie,en,...)
     * used't
     * return a Boolean
     *
     */
    public function createItemMenu($menu_name, $description, $nodeId, $sport, $sport_tags, $sport_id, $region)
    {
        $id_node = \Drupal::entityTypeManager()->getStorage('menu')->loadByProperties(['id' => $menu_name]);
        if (!$id_node) {
            $menu = \Drupal::entityTypeManager()->getStorage('menu')->create([
                'id' => $menu_name,
                'label' => $menu_name,
                'menu_name' => $menu_name,
                'description' => $description,
            ])->save();
            print " Creating Menu $menu_name" . ' - at ' . date("h:i:s") . "\n";
        }
        $menu_link = \Drupal::entityTypeManager()->getStorage('menu_link_content')->loadByProperties([
            'menu_name' => $menu_name,
            'title' => $sport
        ]);


        if (empty($menu_link)) {
            $gs = MenuLinkContent::create([
                'title' => $sport,
                'link' => ['uri' => 'internal:/node/' . $nodeId],
                'description' => $sport_tags,
                'menu_name' => $menu_name,
                'parent' => 'null',
                'expanded' => TRUE,
            ])->save();

            $menu_link_SportObj = \Drupal::entityTypeManager()->getStorage('menu_link_content')->loadByProperties(['description' => $sport_tags]);
            $uuid = reset($menu_link_SportObj)->uuid->value;

            if ($uuid) {
                $menuID = 'main';
                $forside = $sport . ' Forside';
                $this->createGenericItemMenu($menuID, $forside, $nodeId, $uuid, -99);

                $sportPages = new sportBlogPages();

                /*---------------------*/

                $LiveStream = 'Live Stream ' . $sport;
                $url = 'Live Stream ' . $sport;
                $id = 'liveStream';
                $LiveStream_id = $sportPages->createSportInternPages($sport_tags, $sport, $LiveStream, $id, $url, $sport_id, 'sport_stream_reviews', $region);
                $this->createGenericItemMenu($menuID, $LiveStream, $LiveStream_id, $uuid, -98);

                /*---------------------*/

                $Blog = 'Blog ' . $sport;
                $url = 'Blog';
                $id = 'blog';
                $Blog_id = $sportPages->createSportInternBlog($sport_tags, $sport, $Blog, $id, $url, $sport_id, 'sport_blogs', $region);
                $this->createGenericItemMenu($menuID, $Blog, $Blog_id, $uuid, -97);
            }
            print " Creating menuitem $sport on Menu $menu_name" . ' - at ' . date("h:i:s") . "\n";
        }
        return TRUE;
    }

    /**
     * function createChannelsPages ( )
     *
     */
    public function createGenericItemMenu($menuID, $title, $nodeID, $parent, $weight)
    {
        MenuLinkContent::create([
            'title' => $title,
            'link' => ['uri' => 'internal:/node/' . $nodeID],
            'menu_name' => $menuID,
            'expanded' => TRUE,
            'parent' => 'menu_link_content:' . $parent,
            'weight' => $weight,
        ])->save();
        print " Creating menu item '. $title . ' on Menu" . ' - at ' . date("h:i:s") . "\n";
        return TRUE;
    }


    /**
     * function updateMainMenu ( )
     * Desc:
     *  Function to update the main menu...
     * With it we pass to enable or disable sport with out events.
     * return a Boolean but show wish sport is enable or disable in
     * each iteration.
     */
    public function updateMainMenu()
    {

        $menu_link = \Drupal::entityTypeManager()
            ->getStorage('menu_link_content')
            ->loadByProperties(['menu_name' => 'main', 'weight' => '0']);

        foreach ($menu_link as $sport) {
            if ($sport->description->value != 'home') {
                $fromDate = strtotime(date("Y-m-d H:i:s", strtotime("midnight")));
                //$endDate = strtotime(date("Y-m-d H:i:s", strtotime("tomorrow")));
                $endDate = strtotime(date("Y-m-d H:i:s", strtotime("+1 week")));
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
                    print $sport->title->value . " don't have future events, changed to desable." . "\n";
                } else {
                    if ($sport->enabled->value == 0 and $event > 0) {
                        $sport->enabled->value = 1;
                        $sport->save();
                        print $sport->title->value . " have future events, changed to enable" . "\n";
                    }
                }

            }
        }
        return TRUE;
    }

}
