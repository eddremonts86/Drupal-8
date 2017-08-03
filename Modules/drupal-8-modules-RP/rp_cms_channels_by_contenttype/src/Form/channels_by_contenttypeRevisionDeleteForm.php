<?php

namespace Drupal\rp_cms_channels_by_contenttype\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Channels_by_contenttype revision.
 *
 * @ingroup rp_cms_channels_by_contenttype
 */
class channels_by_contenttypeRevisionDeleteForm extends ConfirmFormBase {


  /**
   * The Channels_by_contenttype revision.
   *
   * @var \Drupal\rp_cms_channels_by_contenttype\Entity\channels_by_contenttypeInterface
   */
  protected $revision;

  /**
   * The Channels_by_contenttype storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $channels_by_contenttypeStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new channels_by_contenttypeRevisionDeleteForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The entity storage.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(EntityStorageInterface $entity_storage, Connection $connection) {
    $this->channels_by_contenttypeStorage = $entity_storage;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('channels_by_contenttype'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'channels_by_contenttype_revision_delete_confirm';
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
    return new Url('entity.channels_by_contenttype.version_history', ['channels_by_contenttype' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $channels_by_contenttype_revision = NULL) {
    $this->revision = $this->channels_by_contenttypeStorage->loadRevision($channels_by_contenttype_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->channels_by_contenttypeStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Channels_by_contenttype: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    drupal_set_message(t('Revision from %revision-date of Channels_by_contenttype %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.channels_by_contenttype.canonical',
       ['channels_by_contenttype' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {channels_by_contenttype_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.channels_by_contenttype.version_history',
         ['channels_by_contenttype' => $this->revision->id()]
      );
    }
  }

}
