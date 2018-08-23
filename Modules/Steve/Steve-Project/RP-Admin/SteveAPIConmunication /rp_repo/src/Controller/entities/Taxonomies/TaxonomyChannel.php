<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 4:03 PM
 */

namespace Drupal\rp_repo\Controller\entities\Taxonomies;

use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomy;
use Drupal\rp_api\RPAPIClient;
use Drupal\node\Entity\Node;

class TaxonomyChannel extends taxonomy
{
    /**
     * function createChannelsPages (Import all channels )
     *
     */
  public function getAllchannels()
    {
        $rpClient = RPAPIClient::getClient();
        $AllChannel = $rpClient->getChannels();
        foreach ($AllChannel as $channel) {
            $oldChanel = $this->getTaxonomyByCriterio($channel['id'], 'field_channel_api_id');
            if (empty($oldChanel) or !isset($oldChanel)) {
                $chanelOBJ = [
                    'vid' => 'channels',
                    'name' => $channel['name'],
                    'field_channel_api_id' => $channel['id'],
                    'field_channel_name' => $channel['name'],
                    'field_channel_code' => $channel['name'],
                    'field_channel_des' => $channel['description'],
                    'field_channel_notes' => $channel['notes'],
                ];
                print "New Channel - ".$channel['name']."\n";
                $this->createGenericTaxonomy($chanelOBJ);
                //print ' Creating node (Channels) "' . $channel['name'] . ' - at ' . date("h:i:s") . "\n";
            } else {
                print "Update channel - ". $channel['name']. "\n";
                $oldChanel->field_channel_api_id = $channel['id'];
                $oldChanel->field_channel_name = $channel['name'];
                $oldChanel->field_channel_code = $channel['name'];
                $oldChanel->field_channel_des = $channel['description'];
                $oldChanel->field_channel_notes = $channel['notes'];
                $oldChanel->save();
            }
        }
        return TRUE;
    }
  public function getchannelsbysyte($AllChannel)
  {
    foreach ($AllChannel as $channel) {
      $oldChanel = $this->getTaxonomyByCriterio($channel['id'], 'field_channel_api_id');
      if (empty($oldChanel) or !isset($oldChanel)) {
        print "New Channel - ".$channel['name']."\n";;
        $chanelOBJ = [
          'vid' => 'channels',
          'name' => $channel['name'],
          'field_channel_api_id' => $channel['id'],
          'field_channel_name' => $channel['name'],
          'field_channel_code' => $channel['name'],
          'field_channel_des' => $channel['description'],
          'field_channel_notes' => $channel['notes'],
        ];
        $this->createGenericTaxonomy($chanelOBJ);
        //print ' Creating node (Channels) "' . $channel['name'] . ' - at ' . date("h:i:s") . "\n";
      } else {
        print "Update channel - ". $channel['name']. "\n";
        $oldChanel->field_channel_api_id = $channel['id'];
        $oldChanel->field_channel_name = $channel['name'];
        $oldChanel->field_channel_code = $channel['name'];
        $oldChanel->field_channel_des = $channel['description'];
        $oldChanel->field_channel_notes = $channel['notes'];
        $oldChanel->save();
      }
    }
    return TRUE;
  }




}
