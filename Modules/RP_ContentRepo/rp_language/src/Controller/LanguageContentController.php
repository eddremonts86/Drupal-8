<?php

namespace Drupal\rp_language\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_language\Entity\LanguageContentInterface;

/**
 * Class LanguageContentController.
 *
 *  Returns responses for Language content routes.
 *
 * @package Drupal\rp_language\Controller
 */
class LanguageContentController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Language content  revision.
   *
   * @param int $language_content_revision
   *   The Language content  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($language_content_revision) {
    $language_content = $this->entityManager()->getStorage('language_content')->loadRevision($language_content_revision);
    $view_builder = $this->entityManager()->getViewBuilder('language_content');

    return $view_builder->view($language_content);
  }

  /**
   * Page title callback for a Language content  revision.
   *
   * @param int $language_content_revision
   *   The Language content  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($language_content_revision) {
    $language_content = $this->entityManager()->getStorage('language_content')->loadRevision($language_content_revision);
    return $this->t('Revision of %title from %date', ['%title' => $language_content->label(), '%date' => format_date($language_content->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Language content .
   *
   * @param \Drupal\rp_language\Entity\LanguageContentInterface $language_content
   *   A Language content  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(LanguageContentInterface $language_content) {
    $account = $this->currentUser();
    $langcode = $language_content->language()->getId();
    $langname = $language_content->language()->getName();
    $languages = $language_content->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $language_content_storage = $this->entityManager()->getStorage('language_content');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $language_content->label()]) : $this->t('Revisions for %title', ['%title' => $language_content->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all language content revisions") || $account->hasPermission('administer language content entities')));
    $delete_permission = (($account->hasPermission("delete all language content revisions") || $account->hasPermission('administer language content entities')));

    $rows = [];

    $vids = $language_content_storage->revisionIds($language_content);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_language\LanguageContentInterface $revision */
      $revision = $language_content_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $language_content->getRevisionId()) {
          $link = $this->l($date, new Url('entity.language_content.revision', ['language_content' => $language_content->id(), 'language_content_revision' => $vid]));
        }
        else {
          $link = $language_content->link($date);
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
              Url::fromRoute('entity.language_content.translation_revert', ['language_content' => $language_content->id(), 'language_content_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.language_content.revision_revert', ['language_content' => $language_content->id(), 'language_content_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.language_content.revision_delete', ['language_content' => $language_content->id(), 'language_content_revision' => $vid]),
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

    $build['language_content_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
