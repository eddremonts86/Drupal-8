<?php

namespace Drupal\rp_region\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_region\Entity\regionInterface;
use Drupal\rp_api\RPAPIClient;

/**
 * Class regionController.
 *
 *  Returns responses for Region routes.
 *
 * @package Drupal\rp_region\Controller
 */
class regionController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Region  revision.
   *
   * @param int $region_revision
   *   The Region  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($region_revision) {
    $region = $this->entityManager()->getStorage('region')->loadRevision($region_revision);
    $view_builder = $this->entityManager()->getViewBuilder('region');

    return $view_builder->view($region);
  }

  /**
   * Page title callback for a Region  revision.
   *
   * @param int $region_revision
   *   The Region  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($region_revision) {
    $region = $this->entityManager()->getStorage('region')->loadRevision($region_revision);
    return $this->t('Revision of %title from %date', ['%title' => $region->label(), '%date' => format_date($region->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Region .
   *
   * @param \Drupal\rp_region\Entity\regionInterface $region
   *   A Region  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(regionInterface $region) {
    $account = $this->currentUser();
    $langcode = $region->language()->getId();
    $langname = $region->language()->getName();
    $languages = $region->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $region_storage = $this->entityManager()->getStorage('region');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $region->label()]) : $this->t('Revisions for %title', ['%title' => $region->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all region revisions") || $account->hasPermission('administer region entities')));
    $delete_permission = (($account->hasPermission("delete all region revisions") || $account->hasPermission('administer region entities')));

    $rows = [];

    $vids = $region_storage->revisionIds($region);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_region\regionInterface $revision */
      $revision = $region_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $region->getRevisionId()) {
          $link = $this->l($date, new Url('entity.region.revision', ['region' => $region->id(), 'region_revision' => $vid]));
        }
        else {
          $link = $region->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
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
              Url::fromRoute('entity.region.translation_revert', ['region' => $region->id(), 'region_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.region.revision_revert', ['region' => $region->id(), 'region_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.region.revision_delete', ['region' => $region->id(), 'region_revision' => $vid]),
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

    $build['region_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

  public function importRegions() {
    $rpClient  = RPAPIClient::getClient();
    $regions = $rpClient->getRegion();
    foreach($regions as $region) {
      $data = array(
        'field_region_id' => $region['id'],
        'name' => $region['name'],
        'field_region_label' => $region['label'],
        'field_region_code' => $region['code'],
      );
       $site_new = \Drupal::entityManager()->getStorage('region')->create($data);
      $site_new->save();
    }
    $build['content'] = [
      '#type' => 'item',
      '#title' => t('Content'),
      '#markup' => t('Hello world!'),
    ];
    return $build;
  }

}
