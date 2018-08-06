<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 3/16/17
 * Time: 2:27 PM
 */

namespace Drupal\rebel_endpoints\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;

class Rebel_endpointsAPIUtiles extends ControllerBase
{

    //------------------ Get the new revisions ----------------
    public function getnodesrevisiontime($nid, $chan)
    {
        $new_revision = FALSE;
        $query_selct = \Drupal::database()
            ->select('rebel_endpoins_revisionstatus', 'y1');
        $query_selct->fields('y1', array('change_time', 'nid'));
        $query_selct->condition('y1.nid', $nid, '=');
        $data = $query_selct->execute()->fetchField();
        if ($data) {
            if ((int)$data < (int)$chan) {
                $new_revision = TRUE;
                $query = \Drupal::database()->update('rebel_endpoins_revisionstatus');
                $query->fields(['change_time' => $chan]);
                $query->condition('nid', $nid);
                $query->execute()->rowCount();
            }
        } else {
            $this->savenodesid($nid, $chan);
            $new_revision = TRUE;
        }
        return $new_revision;
    }

    // -----------------Save all revision time nodes by the fist time ------//
    public function savenodesid($nid, $chan)
    {
        $fields = array('nid' => $nid, 'change_time' => $chan);
        $nid = \Drupal::database()->insert('rebel_endpoins_revisionstatus')
            ->fields($fields)
            ->execute();
        if ($nid) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    public function paginations($nids, $page)
    {
        $end = 0;
        $endarray = array();

        if (!isset($page) || $page == 0) {
            foreach ($nids as $nid) {
                $endarray[] = $nid;
                if ($end == 100) {
                    break;
                } else {
                    $end++;
                }
            }
        } else {
            foreach ($nids as $nid) {
                if ($end >= ($page * 100) && $end <= ($page + 1) * 100) {
                    $endarray[] = $nid;
                    $end++;

                } else {
                    if ($end > ($page + 1) * 100) {
                        break;
                    } else {
                        $end++;
                    }
                }
            }
        }
        return $endarray;
    }

    public function getImgUrl_toexport($id,$ulrGlobal='http://51.15.98.16/UserManagement.dk/')
    {
        $imgUrl = '';
        if (isset($id) and $id != NULL and $id != '') {
            $img = File::load($id)->toArray();
            $imgUrl = $img["uri"][0]["value"];
        }
        $imgUrl = str_replace('public://',$ulrGlobal.'sites/default/files/', $imgUrl);
        return $imgUrl;
    }

    public function getTaxonomyByCriterio($obj, $reset = 0)
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

}
