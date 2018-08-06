<?php

namespace Drupal\rebel_endpoints\Controller;

/**
 *
 * @file
 * Contains \Drupal\test_api\Controller\TestAPIController.
 */

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\rebel_endpoints\Controller\Rebel_endpointsAPIUtiles;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveSite;

/* new strategy*/
use Drupal\rp_repo\Controller\entities\System\Users;

class Rebel_endpointsAPIController extends ControllerBase {

  public function getNodes(Request $request) {
    $response = [];
    $response_ = [];
    $count = 0;
    $page = $_GET['page'];
    // -------------------------------- Nodes --------------------------
    $all_nodes = [];

    if (isset($_GET['site'])) {
      $utl = new Rebel_endpointsAPIUtiles();
      $nids = \Drupal::entityQuery('node')
        ->condition('type', $_GET['type'])
        ->execute();
      $endarray = $utl->paginations($nids, $page);
      $nodes = \Drupal\node\Entity\Node::loadMultiple($endarray);

      foreach ($nodes as $node) {
        $nid = $node->nid->value;
        $chan = $node->changed->value;
        //$utl->savenodesid($nid,$chan);
        $all_nodes[] = $node->toArray();
      }
      $response_['nodes'] = $all_nodes;
      $response_['count'] = count($nodes);

    }


    // -------------------------------- end --------------------------

    return new JsonResponse($response);
  }

  public function getNodebyid(Request $request) {

    $response = [];
    $response_ = [];
    // -------------------------------- Nodes --------------------------
    $all_nodes = [];
    $nodes = Node::load($_GET['node']);
    $all_nodes[] = $nodes->toArray();
    $response_['nodes'] = $all_nodes;
    // -------------------------------- end --------------------------
    $response['data'] = $response_;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getNodesbytype(Request $request) {
    $response = [];
    $response_ = [];
    $all_nodes = [];
    $page = $_GET['page'];

    if (isset($_GET['type']) && $_GET['type'] != '') {
      $nids = \Drupal::entityQuery('node')
        ->condition('type', $_GET['type'])
        ->execute();
      $ids = new Rebel_endpointsAPIUtiles();
      $endarray = $ids->paginations($nids, $page);

      $nodes = \Drupal\node\Entity\Node::loadMultiple($endarray);
      foreach ($nodes as $node) {
        $all_nodes[] = $node->toArray();
      }
      $response_['nodes'] = $all_nodes;
      $response_['count'] = count($nodes);
    }
    else {
      $response_['nodes'] = "Need a Content Type..";
      $response_['count'] = 0;
    }
    $response['data'] = $response_;
    $response['actual_page'] = $page;
    $response['total_page'] = (count($nids) / 100);
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getUsers(Request $request) {
    $user = new Users();
    $response = [];
    $response['data']['users'] = $user->getUserList();
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getuserbySiteId(Request $request) {
    $SiteId = $request->attributes->get('SiteId');
    $user = new Users();
    $response = [];
    $response['data']['users'] = $user->getuserbySystemId($SiteId);
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getRules(Request $request) {
    $response = [];
    $query_selct = \Drupal::database()->select('url_alias', 'url');
    $query_selct->fields('url', ['pid', 'alias', 'source']);
    $data = $query_selct->execute()->fetchAll();
    $response['data'] = $data;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getRulesbyID(Request $request) {
    $id = '/node/' . $_GET['node'];
    $response = [];
    $query_selct = \Drupal::database()->select('url_alias', 'url');
    $query_selct->fields('url', ['pid', 'alias', 'source']);
    $query_selct->condition('url.source', $id, '=');
    $data = $query_selct->execute()->fetchAll();
    $response['data'] = $data;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getlastRevision(Request $request) {
    $response = [];
    $response_ = [];
    $all_revisions = [];
    // -------------------------------- Users --------------------------
    $nodes = Node::load($_GET['node']);
    $vids = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadRevision($nodes->vid->value);
    $all_revisions[] = $vids->toArray();
    $response_['revision'] = $all_revisions;
    // -------------------------------- end --------------------------
    $response['data'] = $response_;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getallRevisionbyid(Request $request) {
    // este es una pena
    $nodeid = $_GET['node'];
    $query_selct = \Drupal::database()->select('node_revision', 're');
    $query_selct->fields('re', ['vid']);
    $query_selct->condition('re.nid', $nodeid, '=');
    $data = $query_selct->execute()->fetchAll();

    $all_revisions = [];
    foreach ($data as $dat) {
      $vids = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadRevision($dat->vid);
      $all_revisions[] = $vids->toArray();
    }
    $response_['revision'] = $all_revisions;
    // -------------------------------- end --------------------------
    $response['data'] = $response_;
    $response['method'] = 'GET';
    return new JsonResponse($response);

  }

  public function getRevisionbysite(Request $request) {
    $response = [];
    $response_ = [];
    $all_revisions = [];
    $count = 0;
    $utl = new Rebel_endpointsAPIUtiles();
    // -------------------------------- Users --------------------------
    $nodes = Node::loadMultiple();
    foreach ($nodes as $node) {
      $nid = $node->nid->value;
      $chan = $node->changed->value;
      if (isset($_GET['site']) and @$_GET['site'] != '') {
        if ($node->field_domain_source->target_id == $_GET['site']) {
          $is_new = $utl->getnodesrevisiontime($nid, $chan);
          if ($is_new) {
            $count++;
            $vids = \Drupal::entityTypeManager()
              ->getStorage('node')
              ->loadRevision($node->vid->value);
            $all_revisions[] = $vids->toArray();
          }
        }
      }
    }
    $response_['revision'] = $all_revisions;
    $response_['count'] = $count;
    // -------------------------------- end --------------------------
    $response['data'] = $response_;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getTaxonomy(Request $request) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadMultiple();
    $response = [];
    $response_ = [];
    $count = 0;
    foreach ($taxonomy as $ter) {
      $count++;
      $all_nodes[] = $ter->toArray();
    }
    $response_['nodes'] = $all_nodes;
    $response_['count'] = $count;
    // -------------------------------- end --------------------------
    $response['data'] = $response_;
    $response['method'] = 'GET';
    return new JsonResponse($response);


  }

  public function getCType(Request $request) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('type')
      ->loadMultiple();
    $response = [];
    $response_ = [];
    $count = 0;
    foreach ($taxonomy as $ter) {
      $count++;
      $all_nodes[] = $ter->toArray();
    }
    $response_['nodes'] = $all_nodes;
    $response_['count'] = $count;
    // -------------------------------- end --------------------------
    $response['data'] = $response_;
    $response['method'] = 'GET';
    return new JsonResponse($response);

  }

  /**
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   */
  public function getAPIUserbyTonkesandSite(Request $request) {
    $SiteId = $request->attributes->get('site');
    $siteToken = $request->attributes->get('siteToken');
    $userToken = $request->attributes->get('userToken');
    $siteObj = new taxonomySteveSite();
    $sites = $siteObj->getTaxonomyByOBj([
      'field_api_id' => $SiteId,
      'field_api_id' => $siteToken,
      'vid' => 'steve_site',
    ], 1);
    $users = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties([
        'field_sites_asignated' => $sites,
        'field_token' => $userToken
      ]);
    $user = new Users();
    $response['data']['users'] = $user->getUserList($users->id());
    $response['method'] = 'GET';
    return new JsonResponse($response);


  }

}
