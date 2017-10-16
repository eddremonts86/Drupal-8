<?php

namespace Drupal\rp_cms_steve_integration\Controller;
use Drupal\rp_cms_steve_integration\Controller\SteveBaseControler;

/**
 * Class SteveFrontendControler.
 */
class SteveFrontendControler extends SteveBaseControler {


  public function getPartners($bool) {
    $data = ['vid' => 'stream_provider','field_stream_provider_home_promo' => $bool];
    $partnersList = $this->getTaxonomyByCriterio($data,1);
    $partnersListFormat = [];
    foreach ($partnersList as $partner) {
      $logoid = @$partner->field_streamprovider_logo->target_id;
      $logo = $this->getImgUrl($logoid);
      $url =  $this->getTaxonomyAlias($partner->id());
      $partnersListFormat[] = [
        'name' => $partner->name->value,
        'url' => $url,
        'logo' => $logo,
      ];
    }
    return $partnersListFormat;
  }







}
