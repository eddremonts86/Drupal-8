<?php

namespace Drupal\rp_cms_channels_by_contenttype\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface;

/**
 * Class channels_by_contenttypeController.
 *
 *  Returns responses for Channels_by_contenttype routes.
 */
class channels_by_contenttypeController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Channels_by_contenttype  revision.
   *
   * @param int $channels_by_contenttype_revision
   *   The Channels_by_contenttype  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($channels_by_contenttype_revision) {
    $channels_by_contenttype = $this->entityManager()->getStorage('channels_by_contenttype')->loadRevision($channels_by_contenttype_revision);
    $view_builder = $this->entityManager()->getViewBuilder('channels_by_contenttype');

    return $view_builder->view($channels_by_contenttype);
  }

  /**
   * Page title callback for a Channels_by_contenttype  revision.
   *
   * @param int $channels_by_contenttype_revision
   *   The Channels_by_contenttype  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($channels_by_contenttype_revision) {
    $channels_by_contenttype = $this->entityManager()->getStorage('channels_by_contenttype')->loadRevision($channels_by_contenttype_revision);
    return $this->t('Revision of %title from %date', ['%title' => $channels_by_contenttype->label(), '%date' => format_date($channels_by_contenttype->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Channels_by_contenttype .
   *
   * @param \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface $channels_by_contenttype
   *   A Channels_by_contenttype  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(channels_by_contenttypeInterface $channels_by_contenttype) {
    $account = $this->currentUser();
    $langcode = $channels_by_contenttype->language()->getId();
    $langname = $channels_by_contenttype->language()->getName();
    $languages = $channels_by_contenttype->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $channels_by_contenttype_storage = $this->entityManager()->getStorage('channels_by_contenttype');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $channels_by_contenttype->label()]) : $this->t('Revisions for %title', ['%title' => $channels_by_contenttype->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all channels_by_contenttype revisions") || $account->hasPermission('administer channels_by_contenttype entities')));
    $delete_permission = (($account->hasPermission("delete all channels_by_contenttype revisions") || $account->hasPermission('administer channels_by_contenttype entities')));

    $rows = [];

    $vids = $channels_by_contenttype_storage->revisionIds($channels_by_contenttype);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_cms_channels_by_contenttype\channels_by_contenttypeInterface $revision */
      $revision = $channels_by_contenttype_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $channels_by_contenttype->getRevisionId()) {
          $link = $this->l($date, new Url('entity.channels_by_contenttype.revision', ['channels_by_contenttype' => $channels_by_contenttype->id(), 'channels_by_contenttype_revision' => $vid]));
        }
        else {
          $link = $channels_by_contenttype->link($date);
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
              Url::fromRoute('entity.channels_by_contenttype.translation_revert', ['channels_by_contenttype' => $channels_by_contenttype->id(), 'channels_by_contenttype_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.channels_by_contenttype.revision_revert', ['channels_by_contenttype' => $channels_by_contenttype->id(), 'channels_by_contenttype_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.channels_by_contenttype.revision_delete', ['channels_by_contenttype' => $channels_by_contenttype->id(), 'channels_by_contenttype_revision' => $vid]),
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

    $build['channels_by_contenttype_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
