<?php

namespace Drupal\rp_stream_provider_info\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface;

/**
 * Class StreamProviderInfoController.
 *
 *  Returns responses for Stream Provider Info routes.
 *
 * @package Drupal\rp_stream_provider_info\Controller
 */
class StreamProviderInfoController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Stream Provider Info  revision.
   *
   * @param int $stream_provider_info_revision
   *   The Stream Provider Info  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($stream_provider_info_revision) {
    $stream_provider_info = $this->entityManager()->getStorage('stream_provider_info')->loadRevision($stream_provider_info_revision);
    $view_builder = $this->entityManager()->getViewBuilder('stream_provider_info');

    return $view_builder->view($stream_provider_info);
  }

  /**
   * Page title callback for a Stream Provider Info  revision.
   *
   * @param int $stream_provider_info_revision
   *   The Stream Provider Info  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($stream_provider_info_revision) {
    $stream_provider_info = $this->entityManager()->getStorage('stream_provider_info')->loadRevision($stream_provider_info_revision);
    return $this->t('Revision of %title from %date', ['%title' => $stream_provider_info->label(), '%date' => format_date($stream_provider_info->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Stream Provider Info .
   *
   * @param \Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface $stream_provider_info
   *   A Stream Provider Info  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(StreamProviderInfoInterface $stream_provider_info) {
    $account = $this->currentUser();
    $langcode = $stream_provider_info->language()->getId();
    $langname = $stream_provider_info->language()->getName();
    $languages = $stream_provider_info->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $stream_provider_info_storage = $this->entityManager()->getStorage('stream_provider_info');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $stream_provider_info->label()]) : $this->t('Revisions for %title', ['%title' => $stream_provider_info->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all stream provider info revisions") || $account->hasPermission('administer stream provider info entities')));
    $delete_permission = (($account->hasPermission("delete all stream provider info revisions") || $account->hasPermission('administer stream provider info entities')));

    $rows = [];

    $vids = $stream_provider_info_storage->revisionIds($stream_provider_info);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_stream_provider_info\StreamProviderInfoInterface $revision */
      $revision = $stream_provider_info_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $stream_provider_info->getRevisionId()) {
          $link = $this->l($date, new Url('entity.stream_provider_info.revision', ['stream_provider_info' => $stream_provider_info->id(), 'stream_provider_info_revision' => $vid]));
        }
        else {
          $link = $stream_provider_info->link($date);
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
              Url::fromRoute('entity.stream_provider_info.translation_revert', ['stream_provider_info' => $stream_provider_info->id(), 'stream_provider_info_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.stream_provider_info.revision_revert', ['stream_provider_info' => $stream_provider_info->id(), 'stream_provider_info_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.stream_provider_info.revision_delete', ['stream_provider_info' => $stream_provider_info->id(), 'stream_provider_info_revision' => $vid]),
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

    $build['stream_provider_info_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
