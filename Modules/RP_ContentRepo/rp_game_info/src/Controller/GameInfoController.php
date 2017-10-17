<?php

namespace Drupal\rp_game_info\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_game_info\Entity\GameInfoInterface;

/**
 * Class GameInfoController.
 *
 *  Returns responses for Game info routes.
 *
 * @package Drupal\rp_game_info\Controller
 */
class GameInfoController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Game info  revision.
   *
   * @param int $game_info_revision
   *   The Game info  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($game_info_revision) {
    $game_info = $this->entityManager()->getStorage('game_info')->loadRevision($game_info_revision);
    $view_builder = $this->entityManager()->getViewBuilder('game_info');

    return $view_builder->view($game_info);
  }

  /**
   * Page title callback for a Game info  revision.
   *
   * @param int $game_info_revision
   *   The Game info  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($game_info_revision) {
    $game_info = $this->entityManager()->getStorage('game_info')->loadRevision($game_info_revision);
    return $this->t('Revision of %title from %date', ['%title' => $game_info->label(), '%date' => format_date($game_info->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Game info .
   *
   * @param \Drupal\rp_game_info\Entity\GameInfoInterface $game_info
   *   A Game info  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(GameInfoInterface $game_info) {
    $account = $this->currentUser();
    $langcode = $game_info->language()->getId();
    $langname = $game_info->language()->getName();
    $languages = $game_info->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $game_info_storage = $this->entityManager()->getStorage('game_info');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $game_info->label()]) : $this->t('Revisions for %title', ['%title' => $game_info->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all game info revisions") || $account->hasPermission('administer game info entities')));
    $delete_permission = (($account->hasPermission("delete all game info revisions") || $account->hasPermission('administer game info entities')));

    $rows = [];

    $vids = $game_info_storage->revisionIds($game_info);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_game_info\GameInfoInterface $revision */
      $revision = $game_info_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $game_info->getRevisionId()) {
          $link = $this->l($date, new Url('entity.game_info.revision', ['game_info' => $game_info->id(), 'game_info_revision' => $vid]));
        }
        else {
          $link = $game_info->link($date);
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
              Url::fromRoute('entity.game_info.translation_revert', ['game_info' => $game_info->id(), 'game_info_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.game_info.revision_revert', ['game_info' => $game_info->id(), 'game_info_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.game_info.revision_delete', ['game_info' => $game_info->id(), 'game_info_revision' => $vid]),
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

    $build['game_info_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
