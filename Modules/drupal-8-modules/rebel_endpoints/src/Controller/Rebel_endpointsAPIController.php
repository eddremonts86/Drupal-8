<?php
namespace Drupal\rebel_endpoints\Controller;

/**
 * @file
 * Contains \Drupal\test_api\Controller\TestAPIController.
 */

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Drupal\rebel_endpoints\Controller\Rebel_endpointsAPIUtiles;

class Rebel_endpointsAPIController extends ControllerBase
{

  public function getNodes(Request $request)
  {
    $response = array();
    $response_ = array();
    $count = 0;
    $page = $_GET['page'];
    // -------------------------------- Nodes --------------------------
    $all_nodes = array();

    if(isset($_GET['site'])){
        $utl = new Rebel_endpointsAPIUtiles();
        $nids = \Drupal::entityQuery('node')
                ->condition('type',$_GET['type'])
                ->execute();
        $endarray = $utl->paginations($nids,$page);
        $nodes =  \Drupal\node\Entity\Node::loadMultiple($endarray);

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
  public function getNodebyid(Request $request)
  {

    $response = array();
    $response_ = array();
    // -------------------------------- Nodes --------------------------
    $all_nodes = array();
    $nodes = Node::load($_GET['node']);
    $all_nodes[] = $nodes->toArray();
    $response_['nodes'] = $all_nodes;
    // -------------------------------- end --------------------------
    $response['data'] = $response_;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }
  public function getNodesbytype(Request $request)
  {
    $response = array();
    $response_ = array();
    $all_nodes = array();
    $page = $_GET['page'];

    if(isset($_GET['type']) && $_GET['type'] != ''){
      $nids = \Drupal::entityQuery('node')->condition('type',$_GET['type'])->execute();
      $ids = new Rebel_endpointsAPIUtiles();
      $endarray = $ids->paginations($nids,$page);

      $nodes =  \Drupal\node\Entity\Node::loadMultiple($endarray);
      foreach ($nodes as $node){
        $all_nodes[] = $node->toArray();
      }
      $response_['nodes'] = $all_nodes;
      $response_['count'] = count($nodes);
    }
    else{
      $response_['nodes'] = "Need a Content Type..";
      $response_['count'] = 0;
    }
    $response['data'] = $response_;
    $response['actual_page'] = $page;
    $response['total_page'] = (count($nids)/100);
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getUsers(Request $request)
  {
    $response = array();
    $response_ = array();
    // -------------------------------- Users --------------------------
    $all_user = array();
    $users = User::loadMultiple();
    foreach ($users as $user) {
      $all_user[] = $user->toArray();
    }
    $response_['user'] = $all_user;
    // -------------------------------- end --------------------------
    $response['data'] = $response_;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getRules(Request $request)
  {
    $response = array();
    $query_selct = \Drupal::database()->select('url_alias', 'url');
    $query_selct->fields('url', array('pid', 'alias', 'source'));
    $data = $query_selct->execute()->fetchAll();
    $response['data'] = $data;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }
  public function getRulesbyID(Request $request)
  {
    $id = '/node/' . $_GET['node'];
    $response = array();
    $query_selct = \Drupal::database()->select('url_alias', 'url');
    $query_selct->fields('url', array('pid', 'alias', 'source'));
    $query_selct->condition('url.source', $id, '=');
    $data = $query_selct->execute()->fetchAll();
    $response['data'] = $data;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getlastRevision(Request $request)
  {
    $response = array();
    $response_ = array();
    $all_revisions = array();
    // -------------------------------- Users --------------------------
    $nodes = Node::load($_GET['node']);
    $vids = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($nodes->vid->value);
    $all_revisions[] = $vids->toArray();
    $response_['revision'] = $all_revisions;
    // -------------------------------- end --------------------------
    $response['data'] = $response_;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }
  public function getallRevisionbyid(Request $request){
    // este es una pena
    $nodeid = $_GET['node'];
    $query_selct = \Drupal::database()->select('node_revision', 're');
    $query_selct->fields('re',array('vid'));
    $query_selct->condition('re.nid', $nodeid, '=');
    $data = $query_selct->execute()->fetchAll();

    $all_revisions = array();
    foreach ($data as $dat){
      $vids = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($dat->vid);
      $all_revisions[] = $vids->toArray();
    }
    $response_['revision'] = $all_revisions;
    // -------------------------------- end --------------------------
    $response['data'] = $response_;
    $response['method'] = 'GET';
    return new JsonResponse($response);

  }
  public function getRevisionbysite(Request $request)
  {
    $response = array();
    $response_ = array();
    $all_revisions = array();
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
            $vids = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($node->vid->value);
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

  public function getTaxonomy(Request $request){
    $taxonomy = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple();
    $response = array();
    $response_ = array();
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
  public function getCType(Request $request){
    $taxonomy = \Drupal::entityTypeManager()->getStorage('type')->loadMultiple();
    $response = array();
    $response_ = array();
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

}
