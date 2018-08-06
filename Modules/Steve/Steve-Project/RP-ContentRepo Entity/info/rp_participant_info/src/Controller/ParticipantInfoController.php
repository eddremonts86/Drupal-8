<?php

namespace Drupal\rp_participant_info\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_participant_info\Entity\ParticipantInfoInterface;

/**
 * Class ParticipantInfoController.
 *
 *  Returns responses for Participant Info routes.
 *
 * @package Drupal\rp_participant_info\Controller
 */
class ParticipantInfoController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Participant Info  revision.
   *
   * @param int $participant_info_revision
   *   The Participant Info  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($participant_info_revision) {
    $participant_info = $this->entityManager()->getStorage('participant_info')->loadRevision($participant_info_revision);
    $view_builder = $this->entityManager()->getViewBuilder('participant_info');

    return $view_builder->view($participant_info);
  }

  /**
   * Page title callback for a Participant Info  revision.
   *
   * @param int $participant_info_revision
   *   The Participant Info  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($participant_info_revision) {
    $participant_info = $this->entityManager()->getStorage('participant_info')->loadRevision($participant_info_revision);
    return $this->t('Revision of %title from %date', ['%title' => $participant_info->label(), '%date' => format_date($participant_info->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Participant Info .
   *
   * @param \Drupal\rp_participant_info\Entity\ParticipantInfoInterface $participant_info
   *   A Participant Info  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ParticipantInfoInterface $participant_info) {
    $account = $this->currentUser();
    $langcode = $participant_info->language()->getId();
    $langname = $participant_info->language()->getName();
    $languages = $participant_info->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $participant_info_storage = $this->entityManager()->getStorage('participant_info');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $participant_info->label()]) : $this->t('Revisions for %title', ['%title' => $participant_info->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all participant info revisions") || $account->hasPermission('administer participant info entities')));
    $delete_permission = (($account->hasPermission("delete all participant info revisions") || $account->hasPermission('administer participant info entities')));

    $rows = [];

    $vids = $participant_info_storage->revisionIds($participant_info);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_participant_info\ParticipantInfoInterface $revision */
      $revision = $participant_info_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $participant_info->getRevisionId()) {
          $link = $this->l($date, new Url('entity.participant_info.revision', ['participant_info' => $participant_info->id(), 'participant_info_revision' => $vid]));
        }
        else {
          $link = $participant_info->link($date);
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
              Url::fromRoute('entity.participant_info.translation_revert', ['participant_info' => $participant_info->id(), 'participant_info_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.participant_info.revision_revert', ['participant_info' => $participant_info->id(), 'participant_info_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.participant_info.revision_delete', ['participant_info' => $participant_info->id(), 'participant_info_revision' => $vid]),
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

    $build['participant_info_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
