<?php

namespace Drupal\rp_channel\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_channel\Entity\ChannelInterface;

/**
 * Class ChannelController.
 *
 *  Returns responses for Channel routes.
 *
 * @package Drupal\rp_channel\Controller
 */
class ChannelController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Channel  revision.
   *
   * @param int $channel_revision
   *   The Channel  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($channel_revision) {
    $channel = $this->entityManager()->getStorage('channel')->loadRevision($channel_revision);
    $view_builder = $this->entityManager()->getViewBuilder('channel');

    return $view_builder->view($channel);
  }

  /**
   * Page title callback for a Channel  revision.
   *
   * @param int $channel_revision
   *   The Channel  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($channel_revision) {
    $channel = $this->entityManager()->getStorage('channel')->loadRevision($channel_revision);
    return $this->t('Revision of %title from %date', ['%title' => $channel->label(), '%date' => format_date($channel->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Channel .
   *
   * @param \Drupal\rp_channel\Entity\ChannelInterface $channel
   *   A Channel  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ChannelInterface $channel) {
    $account = $this->currentUser();
    $langcode = $channel->language()->getId();
    $langname = $channel->language()->getName();
    $languages = $channel->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $channel_storage = $this->entityManager()->getStorage('channel');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $channel->label()]) : $this->t('Revisions for %title', ['%title' => $channel->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all channel revisions") || $account->hasPermission('administer channel entities')));
    $delete_permission = (($account->hasPermission("delete all channel revisions") || $account->hasPermission('administer channel entities')));

    $rows = [];

    $vids = $channel_storage->revisionIds($channel);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_channel\ChannelInterface $revision */
      $revision = $channel_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $channel->getRevisionId()) {
          $link = $this->l($date, new Url('entity.channel.revision', ['channel' => $channel->id(), 'channel_revision' => $vid]));
        }
        else {
          $link = $channel->link($date);
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
              Url::fromRoute('entity.channel.translation_revert', ['channel' => $channel->id(), 'channel_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.channel.revision_revert', ['channel' => $channel->id(), 'channel_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.channel.revision_delete', ['channel' => $channel->id(), 'channel_revision' => $vid]),
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

    $build['channel_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
