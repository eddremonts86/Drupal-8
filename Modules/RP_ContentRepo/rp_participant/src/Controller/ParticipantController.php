<?php

namespace Drupal\rp_participant\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_participant\Entity\ParticipantInterface;

/**
 * Class ParticipantController.
 *
 *  Returns responses for Participant routes.
 *
 * @package Drupal\rp_participant\Controller
 */
class ParticipantController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Participant  revision.
   *
   * @param int $participant_revision
   *   The Participant  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($participant_revision) {
    $participant = $this->entityManager()->getStorage('participant')->loadRevision($participant_revision);
    $view_builder = $this->entityManager()->getViewBuilder('participant');

    return $view_builder->view($participant);
  }

  /**
   * Page title callback for a Participant  revision.
   *
   * @param int $participant_revision
   *   The Participant  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($participant_revision) {
    $participant = $this->entityManager()->getStorage('participant')->loadRevision($participant_revision);
    return $this->t('Revision of %title from %date', ['%title' => $participant->label(), '%date' => format_date($participant->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Participant .
   *
   * @param \Drupal\rp_participant\Entity\ParticipantInterface $participant
   *   A Participant  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ParticipantInterface $participant) {
    $account = $this->currentUser();
    $langcode = $participant->language()->getId();
    $langname = $participant->language()->getName();
    $languages = $participant->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $participant_storage = $this->entityManager()->getStorage('participant');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $participant->label()]) : $this->t('Revisions for %title', ['%title' => $participant->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all participant revisions") || $account->hasPermission('administer participant entities')));
    $delete_permission = (($account->hasPermission("delete all participant revisions") || $account->hasPermission('administer participant entities')));

    $rows = [];

    $vids = $participant_storage->revisionIds($participant);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_participant\ParticipantInterface $revision */
      $revision = $participant_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $participant->getRevisionId()) {
          $link = $this->l($date, new Url('entity.participant.revision', ['participant' => $participant->id(), 'participant_revision' => $vid]));
        }
        else {
          $link = $participant->link($date);
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
              Url::fromRoute('entity.participant.translation_revert', ['participant' => $participant->id(), 'participant_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.participant.revision_revert', ['participant' => $participant->id(), 'participant_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.participant.revision_delete', ['participant' => $participant->id(), 'participant_revision' => $vid]),
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

    $build['participant_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
