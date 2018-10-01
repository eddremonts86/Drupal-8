<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 4:08 PM
 */

namespace Drupal\rp_repo\Controller\entities\Taxonomies;

use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomy;

class taxonomyJsonLD extends taxonomy

{

  public function createJsonLdTaxonomy
  ($voc = 'jsonld_',
   $data = array(
     'Events',
     'Leages',
     'Participants',
     'Personal',
     'Place',
     'Site',
     'Streams',
     'Sport',
     'Reviews',
     'Blog')
  )
  {
    foreach ($data as $tName) {
      $this->createTaxonomyByNameAndVoc($tName, $voc);
    }
    return true;
  }


}
