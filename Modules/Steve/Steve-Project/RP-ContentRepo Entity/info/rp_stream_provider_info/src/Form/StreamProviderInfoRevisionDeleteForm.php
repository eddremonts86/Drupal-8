<?php

namespace Drupal\rp_stream_provider_info\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Stream Provider Info revision.
 *
 * @ingroup rp_stream_provider_info
 */
class StreamProviderInfoRevisionDeleteForm extends ConfirmFormBase {


  /**
   * The Stream Provider Info revision.
   *
   * @var \Drupal\rp_stream_provider_info\Entity\StreamProviderInfoInterface
   */
  protected $revision;

  /**
   * The Stream Provider Info storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $StreamProviderInfoStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new StreamProviderInfoRevisionDeleteForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The entity storage.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(EntityStorageInterface $entity_storage, Connection $connection) {
    $this->StreamProviderInfoStorage = $entity_storage;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('stream_provider_info'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'stream_provider_info_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete the revision from %revision-date?', ['%revision-date' => format_date($this->revision->getRevisionCreationTime())]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.stream_provider_info.version_history', ['stream_provider_info' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $stream_provider_info_revision = NULL) {
    $this->revision = $this->StreamProviderInfoStorage->loadRevision($stream_provider_info_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->StreamProviderInfoStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Stream Provider Info: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    drupal_set_message(t('Revision from %revision-date of Stream Provider Info %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.stream_provider_info.canonical',
       ['stream_provider_info' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {stream_provider_info_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.stream_provider_info.version_history',
         ['stream_provider_info' => $this->revision->id()]
      );
    }
  }

}
