<?php
/**
 * @file
 * Contains \Drupal\steve\SteveData.
 */

namespace Drupal\steve;

use Drupal as Drupal;
use Drupal\node\Entity\Node as Node;
use Drupal\file\Entity\File as File;

/**
 * The primary class for the Drupal Bootstrap base theme.
 *
 * Provides many helper methods.
 *
 * @ingroup utility
 */
class SteveData
{
    public static function get_calendar($count = 10)
    {
        $schedule = self::get_schedule_tree(10)['AllEvents'];
        $data = array();

        foreach($schedule as $day => $tournament)
        {
            $day_data = array(
                'date' => $day,
                'events' => array()
            );
            foreach($tournament as $t)
            {
                foreach($t['events'] as $event)
                {
                    $provider_ids = $event['field_stream_provider_gp'];
                    $providers = array();

                    foreach($provider_ids as $provider_id)
                    {
                        // $provider_data = self::get_entities('stream_provider', 'nid', $provider_id);
                        // TODO hardcoded!
                        $provider_link = array(
                            'href' => '#',
                            'image' => 'themes/custom/steve/images/provider_logo_square.png'
                        );
                        $providers[] = $provider_link;
                    }

                    // appends event data to day data
                    $day_data['events'][] = array(
                        'participants' => self::get_value($event['field_participant_1']) . ' ' . t('V') . ' ' . self::get_value($event['field_participant_2']),
                        'tournament' => self::get_value($event['field_game_tournament']),
                        'providers' => $providers
                    );
                }
            }
            $data[] = $day_data;
        }

        return $data;
    }

    public static function get_stream_providers($mode = '')
    {
        $raw_providers = self::fetch_stream_providers();
        $providers = [];

        // TODO modify this once data is set
        foreach ($raw_providers as $raw_provider)
        {
            $provider = array(
                'id' => self::get_value($raw_provider['nid']),
                'title' => $raw_provider['title'],
                'rating' => self::get_rating($raw_provider['field_properties_rating']),
                'quality' => self::get_value($raw_provider['field_properties_video_quality']),
                'price' => self::get_value($raw_provider['field_properties_price']),
                'details' => array(
                    'header' => self::get_value($raw_provider['field_ppc_lsp_pd_h']),
                    'sections' => array(
                        'quality' => array(
                            'header' => self::get_value($raw_provider['field_ppc_qs_h']),
                            'content' => self::get_value($raw_provider['field_ppc_us_c'])
                        ),
                        'committee' => array(
                            'header' => self::get_value($raw_provider['field_ppc_lsp_pd_h']),
                            'content' => self::get_value($raw_provider['field_ppc_us_c'])
                        ),
                        'price' => array(
                            'header' => self::get_value($raw_provider['field_ppc_ps_h']),
                            'content' => self::get_value($raw_provider['field_ppc_ps_c'])
                        ),
                        'how_to' => array(
                            'header' => self::get_value($raw_provider['field_ppc_lsp_pd_pn']),
                            'content' => array(
                                array(
                                    'title' => self::get_value($raw_provider['field_ppc_lsp_pd_ione_h']),
                                    // 'icon' => self::get_value($raw_provider[]), TODO whats the field containing this image ??
                                    'icon' => 'themes/custom/steve/images/ico-green-1.png',
                                    'subtitle' => self::get_value($raw_provider['field_ppc_lsp_pd_ione_sh'])
                                ),
                                array(
                                    'title' => self::get_value($raw_provider['field_ppc_lsp_pd_itow_h']),
                                    // 'icon' => self::get_value($raw_provider[]), TODO whats the field containing this image ??
                                    'icon' => 'themes/custom/steve/images/ico-green-2.png',
                                    'subtitle' => self::get_value($raw_provider['field_ppc_lsp_pd_itow_sh'])
                                ),
                                array(
                                    'title' => self::get_value($raw_provider['field_ppc_lsp_pd_ithree_h']),
                                    // 'icon' => self::get_value($raw_provider[]), TODO whats the field containing this image ??
                                    'icon' => 'themes/custom/steve/images/ico-green-3.png',
                                    'subtitle' => self::get_value($raw_provider['field_ppc_lsp_pd_ithree_sh']),
                                    'notice' => self::get_value($raw_provider['field_ppc_lsp_pd_disc']),
                                    'notice_link' => array( // TODO again, whats the fields containing this info???
                                        'label' => 'Regler & Vilkår',
                                    )
                                )
                            ),
                            'arrow_image' => 'themes/custom/steve/images/line-arw.png'
                        )
                    ),
                    'buttons' => array(
                        // array( TODO from where catch grey button that appears for some streamers?
                        //     'text' => SteveData::get_value($raw_provider['field_ppc_lsp_pd_bh']),
                        //     'url' => SteveData::get_value($raw_provider['field_ppc_lsp_pd_bsh'])
                        // ),
                        array( // TODO hardcoded stuff for above
                            'label' => 'Læs anmeldelse',
                            'url' => 'url-to-reviews',
                            'class' => 'gray'
                        ),
                        array(
                            'label' => self::get_value($raw_provider['field_ppc_lsp_pd_bh']),
                            'url' => self::get_value($raw_provider['field_ppc_lsp_pd_bsh'])
                        )
                    )
                )
            );

            if ($mode === 'list_large')
            {
                $provider['logo'] = 'themes/custom/steve/images/stream-provider-1.png';
            }
            else
            {
                $provider['logo'] = self::get_image_uri($raw_provider['field_aff_images']);
            }


            $providers[] = $provider;
        }

        return $providers;
    }

    public static function get_url($item)
    {
        $url = Drupal::service('path.alias_manager')->getAliasByPath('/node/' . SteveData::get_value($item['nid']));

        return substr(base_path(), 0, -1) . $url;
    }

    public static function get_rating($arg)
    {
        if (gettype($arg) == 'string')
        {
            $value = (float) $arg;
        }
        else
        {
            $value = self::get_value_as_float($arg);
        }

        $max = 5;
        $has_comma = substr_count((string)$value,',') > 0;
        $int = (int) $value;
        $rating = [];

        for ($i = 1; $i <= $max; $i++)
        {
            if ($has_comma)
            {
                if ($i <= $value)
                {
                    $rating[] = 'star';
                }
                else if ($i > $value && $i == $int)
                {
                    $rating[] = 'star-half-o';
                }
                else if ($i > $value && $i > $int)
                {
                    $rating[] = 'star-o';
                }
            }
            else
            {
                if ($i <= $value)
                {
                    $rating[] = 'star';
                }
                else if ($i > $value)
                {
                    $rating[] = 'star-o';
                }
            }
        }

        return $rating;
    }

    public static function get_value($array)
    {
        $value = '';

        if (isset($array[0]))
        {
            $value = $array[0]['value'];
        }

        return $value;
    }

    public static function get_value_as_float($array)
    {
        $value = self::get_value($array);
        $value_as_float = (float) $value;

        return $value_as_float;
    }

    public static function get_image_uri($image_data)
    {
        $uri = '';
        $id = false;

        if (isset($image_data[0]))
        {
            $id= $image_data[0]['target_id'];
        }

        if ($id)
        {
            $entity = File::load($id)->toArray();
            $uri = $entity['uri'][0]['value'];
        }

        return $uri;
    }

    // TODO: WATCH OUT! this is temporal code. We have to discuss a solution.
    public static function get_site_base_class($current_url)
    {
        $site_base_class = '';

        if (substr_count($current_url, 'fodbold') > 0)
        {
            $site_base_class = 'soccer';
        }
        else if (substr_count($current_url, 'handbold') > 0)
        {
            $site_base_class = 'handball';
        } else if (substr_count($current_url, 'tennis') > 0)
        {
            $site_base_class = 'tennis';
        }

        return $site_base_class;
    }

    /**
     * $type {String}
     * $opc {String} (optional)
     * $value {String} (optional)
     */
    public static function get_entities($entity_type, $field, $value, $count = NULL)
    {
        $all_nodes = "";
        $nids = Drupal::entityQuery($entity_type)->condition($field, $value)->execute();
        $nodes = Node::loadMultiple($nids);

        foreach ($nodes as $node)
        {
            $all_nodes[] = $node->toArray();
        }

        if ($count)
        {
            $all_nodes = array_slice($all_nodes, 0, $count);
        }

        return $all_nodes;
    }

    public static function fetch_stream_providers()
    {
        $query = \Drupal::entityQuery('node');
        $query->condition('status', 1);
        $query->condition('type', 'stream_provider');
        $query->sort('field_properties_rating', 'ASC');
        $ids = $query->execute();
        $all_nodes = self::get_nodes($ids);

        return $all_nodes;
    }

    public static function get_schedule_tree($range = 0, $sport_name = 'Fodbold', $format = "Y-m-d")
    {
      $nodes = self::get_schedule($range, $sport_name);
      $tree = self::get_tree($nodes, $format);

      return $tree;
    }

    private static function get_schedule($range = 0, $sport_name = 'Fodbold')
    {
      $sport = self::get_taxonomy($sport_name);
      $sport = reset($sport);
      $query = \Drupal::entityQuery('node');
      $query->condition('status', 1);
      $query->condition('type', 'game_pages');
      $query->condition('field_tags', $sport->id());
      //$fecha = strtotime(date('Y-05-j'));
      //$query->condition('field_game_date',$fecha, '<');
      $query->sort('field_game_date', 'ASC');
      $query->sort('field_game_tournament_api_id', 'ASC');
      if ($range != 0) {
        $query->range(0, $range);
      }
      $ids = $query->execute();
      $all_nodes = self::get_nodes($ids);


      return $all_nodes;
    }

    private static function get_tree($data, $format)
    {
      $tree = array(
          'AllEvents' => array()
      );

      foreach ($data as $event) {
        $date = date($format, $event['field_game_date'][0]['value']);
        $league = $event['field_game_tournament'][0]['value'];
        $tournament_id = $event['field_game_tournament_api_id'][0]['value'];

        if (!$tree['AllEvents'][$date])
        {
          $tree['AllEvents'][$date] = [];
          $tree['AllEvents'][$date][$league]['events'] = [];
          $tree['AllEvents'][$date][$league]['tournament'] = $league;
          $tree['AllEvents'][$date][$league]['tournament_id'] = $tournament_id;
          array_push($tree['AllEvents'][$date][$league]['events'], $event);
        }
        else
        {
          if ($tree['AllEvents'][$date])
          {
            if (!$tree['AllEvents'][$date][$league] and $tree['AllEvents'][$date][$league]['tournament_id'] != $tournament_id)
            {
              $tree['AllEvents'][$date][$league]['tournament'] = $league;
              $tree['AllEvents'][$date][$league]['tournament_id'] = $tournament_id;
              $tree['AllEvents'][$date][$league]['events'] = [];
              array_push($tree['AllEvents'][$date][$league]['events'], $event);
            }
            else
            {
              array_push($tree['AllEvents'][$date][$league]['events'], $event);
            }
          }
        }
      }

      return $tree;
    }

    public static function get_taxonomy($name)
    {
        $taxonomy = \Drupal::entityTypeManager()
                              ->getStorage('taxonomy_term')
                              ->loadByProperties(['name' => $name]);

        return $taxonomy;
    }

    public static function get_nodes($ids)
    {
        $all_nodes = [];

        foreach ($ids as $id)
        {
            $node = Node::load($id);
            $all_nodes [] = $node->toArray();
        }

        return $all_nodes;
    }

    /**
     * $type {String}
     * $opc {String} (optional)
     * $value {String} (optional)
     */
    public static function get_node($type, $opc = null, $value = null)
    {
        $node = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(array('type' => $type, $opc => $name));
    }

    public static function get_data($content_type)
    {
        $all_nodes = "";
        $nids = \Drupal::entityQuery('node')->condition('type', $content_type)
                                            ->execute();
        $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
        foreach ($nodes as $node) {
            $all_nodes[] = $node->toArray();
        }
        return $all_nodes;
    }



}
