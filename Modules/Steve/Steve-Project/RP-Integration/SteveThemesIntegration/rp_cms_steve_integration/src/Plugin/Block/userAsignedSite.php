<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveSite;

/**
 * Provides a 'userAsignedSite' block.
 *
 * @Block(
 *  id = "user_asigned_site",
 *  admin_label = @Translation("User - Asigned Site"),
 * )
 */
class userAsignedSite extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $current_user = \Drupal::currentUser();
    $user = User::load($current_user->id());
    $taxonomySteveSite = new taxonomySteveSite();
    $links="";
    $AllSite = [];
    $U = $user->toArray();
    foreach ($U['field_sites_asignated'] as $AllsitesId) {
      $sites = $taxonomySteveSite->getTaxonomyByOBj([
        'tid' => $AllsitesId['target_id'],
        'vid' => 'steve_site',
      ], 'obj');

      foreach ($sites as $newsite) {
        $AllSite[] = [
          'api_id' => $newsite->field_api_id->value,
          'site_name' => $newsite->name->value,
          'site_url' => $newsite->field_site_url->value,
          'site_token' => $newsite->field_site_token->value,


        ];
        $links = $links."<div class=''><a target='_blank' class='button button-action button button--primary js-form-submit form-submit btn-success btn-lg btn icon-before' 
        href='".$newsite->field_site_url->value . '/steveAPI/'. $U['field_token'][0]['value'].'/'.$newsite->field_site_token->value."'>".$newsite->name->value . "</a><br><div>";

      }
    }
    $build['user_asigned_site']['#markup'] = "
      <div class='outer-wrapper'> 
      <h3>User Asigned Site - Go to asignated Sites</h3>
      <hr>$links<hr></div>";
    return $build;
  }

}
