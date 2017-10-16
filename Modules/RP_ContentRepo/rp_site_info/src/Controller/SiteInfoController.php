<?php

namespace Drupal\rp_site_info\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_site_info\Entity\SiteInfoInterface;

/**
 * Class SiteInfoController.
 *
 *  Returns responses for Site info routes.
 *
 * @package Drupal\rp_site_info\Controller
 */
class SiteInfoController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Site info  revision.
   *
   * @param int $site_info_revision
   *   The Site info  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($site_info_revision) {
    $site_info = $this->entityManager()->getStorage('site_info')->loadRevision($site_info_revision);
    $view_builder = $this->entityManager()->getViewBuilder('site_info');

    return $view_builder->view($site_info);
  }

  /**
   * Page title callback for a Site info  revision.
   *
   * @param int $site_info_revision
   *   The Site info  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($site_info_revision) {
    $site_info = $this->entityManager()->getStorage('site_info')->loadRevision($site_info_revision);
    return $this->t('Revision of %title from %date', ['%title' => $site_info->label(), '%date' => format_date($site_info->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Site info .
   *
   * @param \Drupal\rp_site_info\Entity\SiteInfoInterface $site_info
   *   A Site info  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(SiteInfoInterface $site_info) {
    $account = $this->currentUser();
    $langcode = $site_info->language()->getId();
    $langname = $site_info->language()->getName();
    $languages = $site_info->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $site_info_storage = $this->entityManager()->getStorage('site_info');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $site_info->label()]) : $this->t('Revisions for %title', ['%title' => $site_info->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all site info revisions") || $account->hasPermission('administer site info entities')));
    $delete_permission = (($account->hasPermission("delete all site info revisions") || $account->hasPermission('administer site info entities')));

    $rows = [];

    $vids = $site_info_storage->revisionIds($site_info);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_site_info\SiteInfoInterface $revision */
      $revision = $site_info_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $site_info->getRevisionId()) {
          $link = $this->l($date, new Url('entity.site_info.revision', ['site_info' => $site_info->id(), 'site_info_revision' => $vid]));
        }
        else {
          $link = $site_info->link($date);
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
              Url::fromRoute('entity.site_info.translation_revert', ['site_info' => $site_info->id(), 'site_info_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.site_info.revision_revert', ['site_info' => $site_info->id(), 'site_info_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.site_info.revision_delete', ['site_info' => $site_info->id(), 'site_info_revision' => $vid]),
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

    $build['site_info_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
