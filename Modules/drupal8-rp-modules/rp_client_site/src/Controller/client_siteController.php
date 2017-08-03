<?php

namespace Drupal\rp_client_site\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_client_site\Entity\client_siteInterface;

use Drupal\rp_repo\RepoService;

/**
 * Class client_siteController.
 *
 *  Returns responses for Client_site routes.
 *
 * @package Drupal\rp_client_site\Controller
 */
class client_siteController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Client_site  revision.
   *
   * @param int $client_site_revision
   *   The Client_site  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($client_site_revision) {
    $client_site = $this->entityManager()
      ->getStorage('client_site')
      ->loadRevision($client_site_revision);
    $view_builder = $this->entityManager()->getViewBuilder('client_site');

    return $view_builder->view($client_site);
  }

  /**
   * Page title callback for a Client_site  revision.
   *
   * @param int $client_site_revision
   *   The Client_site  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($client_site_revision) {
    $client_site = $this->entityManager()
      ->getStorage('client_site')
      ->loadRevision($client_site_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $client_site->label(),
      '%date' => format_date($client_site->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Client_site .
   *
   * @param \Drupal\rp_client_site\Entity\client_siteInterface $client_site
   *   A Client_site  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(client_siteInterface $client_site) {
    $account = $this->currentUser();
    $langcode = $client_site->language()->getId();
    $langname = $client_site->language()->getName();
    $languages = $client_site->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $client_site_storage = $this->entityManager()->getStorage('client_site');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', [
      '@langname' => $langname,
      '%title' => $client_site->label(),
    ]) : $this->t('Revisions for %title', ['%title' => $client_site->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all client_site revisions") || $account->hasPermission('administer client_site entities')));
    $delete_permission = (($account->hasPermission("delete all client_site revisions") || $account->hasPermission('administer client_site entities')));

    $rows = [];

    $vids = $client_site_storage->revisionIds($client_site);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_client_site\client_siteInterface $revision */
      $revision = $client_site_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)
          ->isRevisionTranslationAffected()
      ) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')
          ->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $client_site->getRevisionId()) {
          $link = $this->l($date, new Url('entity.client_site.revision', [
            'client_site' => $client_site->id(),
            'client_site_revision' => $vid,
          ]));
        }
        else {
          $link = $client_site->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')
                ->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
                Url::fromRoute('entity.client_site.translation_revert', [
                  'client_site' => $client_site->id(),
                  'client_site_revision' => $vid,
                  'langcode' => $langcode,
                ]) :
                Url::fromRoute('entity.client_site.revision_revert', [
                  'client_site' => $client_site->id(),
                  'client_site_revision' => $vid,
                ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.client_site.revision_delete', [
                'client_site' => $client_site->id(),
                'client_site_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['client_site_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

  public function importSiteData() {
    $rpClient = RepoService::getClient();
    $regions = $rpClient->getSites();
    foreach ($regions as $region) {
      $id = $region['field_site_api_id'][0]['value'];
      $site = \Drupal::entityManager()->getStorage('client_site')->loadByProperties(['field_site_client_api_id' => $id]);
      if ($site) {
        $this->updateData($region, $site);
      }
      else {
        $this->saveData($region);
      }

    }
    $build['content'] = [
      '#type' => 'item',
      '#title' => t('Content'),
      '#markup' => t('Hello world!'),
    ];
    return $build;
  }

  public function saveData($region) {
    $i = 0;
    $m = explode(',', $region['field_site_channels'][0]['value']);
    $channels = [];
    foreach ($m as $mm) {$channels[$i] = $mm;$i++;}
    $data = [
      'field_site_client_api_id' => $region['field_site_api_id'][0]['value'],
      'field_site_client_channels' => $channels,
      'field_site_client_label' => $region['field_site_label'][0]['value'],
      'field_site_client_languages' => $region['field_site_languages'][0]['value'],
      'field_site_client_name' => $region['field_site_label'][0]['value'],
      'name' => $region['field_site_label'][0]['value'],
      'field_site_client_site_group_nam' => $region['field_site_site_group_name'][0]['value'],
    ];
    $site_new = \Drupal::entityManager()->getStorage('client_site')->create($data);
    $site_new->save();
    return TRUE;
  }

  public function updateData($region, $site) {
    $i = 0;
    $m = explode(',', $region['field_site_channels'][0]['value']);
    $channels = [];
    foreach ($m as $mm) {$channels[$i] = $mm;$i++;}
    $data = [
      'field_site_client_api_id' => $region['field_site_api_id'][0]['value'],
      'field_site_client_channels' => $channels,
      'field_site_client_label' => $region['field_site_label'][0]['value'],
      'field_site_client_languages' => $region['field_site_languages'][0]['value'],
      'field_site_client_name' => $region['field_site_label'][0]['value'],
      'name' => $region['field_site_label'][0]['value'],
      'field_site_client_site_group_nam' => $region['field_site_site_group_name'][0]['value'],
    ];
    //$site_new = \Drupal::entityManager()->getStorage('client_site')->;
    //$site_new->save();
    return TRUE;
  }


}
