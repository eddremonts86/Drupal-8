<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 4:01 PM
 */

namespace Drupal\rp_repo\Controller\entities\Taxonomies;

use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomy;
use Drupal\rp_repo\Controller\entities\Generales\support;
use Drupal\rp_api\RPAPIClient;

class taxonomyStream extends taxonomy
{
    public function createStreamByID($streamsID)
    {
        $rpClient = RPAPIClient::getClient();
        $streamObj = $rpClient->getStreamprovidersbyID(['id' => $streamsID]);
        $stream[0] = [
            'name' => $streamObj['name'],
            'id' => $streamObj['id'],
            'type' => array(
                'name' => $streamObj['type']['name'],
                'type' => $streamObj['type']['type']
            ),
        ];
        print "New Stream - ".$streamObj['type']['name']."\n";
        return $this->createStreamPages($stream);
    }

    /**
     * function createStreamPages ()
     *
     * - $streams - Array of stream from Steve API
     *   streamprovider: [{
     * id: 1,
     * meta: [
     * { channel: 1},
     * {channel: 2},
     * {channel: 3},
     * {channel: 4}
     * ]
     * }...]
     * - $sport_tags - Taxonomy ID to sport  (Example. 2145)
     */
    public function createStreamPages($streams)
    {
        $tags_array = array();
        foreach ($streams as $stream) {
            $requestStream = array(
                'field_api_id' => $stream['id'],
                'name' => $stream['name']);

            $taxonomyStream = $this->getTaxonomyByOBj($requestStream,1);
            if (empty($taxonomyStream)) {
                $rpClient = RPAPIClient::getClient();
                $streamObj = $rpClient->getStreamprovidersbyID(['id' => $stream['id']]);
                $streamType = $streamObj['type'];
                $requestType = array('field_api_id' => $streamType['id'], 'name' => $streamType['name']);
                $taxonomyStreamType = $this->getTaxonomyByCriterioMultiple($requestType);

                if (empty($taxonomyStreamType)) {
                    $obj = [
                        'name' => $streamType['name'],
                        'vid' => 'stream_provider',
                        'field_api_id' => 'type_'.$streamType['id'],
                        'field_jsonld_struct' => $this->getTaxonomyByOBj(array('vid' => 'jsonld_', 'name' => 'Streams'),1)
                    ];
                  print "New Stream - ".$streamType['name']."\n";
                    $this->createGenericTaxonomy($obj);
                }
                if (empty($taxonomyStream)) {
                    $requestType = array('field_api_id' => 'type_'.$streamType['id'], 'name' => $streamType['name']);
                    $parent = $this->getTaxonomyByCriterioMultiple($requestType);
                    $obj = [
                        'name' => $streamObj['name'],
                        'vid' => 'stream_provider',
                        'parent' => $parent,
                        'field_api_id' => $streamType['id'],
                        'field_jsonld_struct' => $this->getTaxonomyByOBj(array('vid' => 'jsonld_', 'name' => 'Streams'),1)
                    ];
                    print "New Stream - ".$streamObj['name']."\n";
                    $taxonomystream = $this->createGenericTaxonomy($obj, false);
                    $taxonomystreamId = $taxonomystream->id();
                    $tags_array [] = ['target_id' => $taxonomystreamId];
                }

            } else {
                $tags_array [] = ['target_id' => $taxonomyStream];
            }
        }
        return $tags_array;
    }


}
