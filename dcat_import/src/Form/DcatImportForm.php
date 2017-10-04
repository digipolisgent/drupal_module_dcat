<?php

namespace Drupal\dcat_import\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\dcat_import\Entity\DcatSourceInterface;

/**
 * Configure dcat import settings for this site.
 */
class DcatImportForm extends ConfirmFormBase {

  /**
   * The DCAT source entity.
   *
   * @var \Drupal\dcat_import\Entity\DcatSourceInterface
   */
  protected $dcatSource;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dcat_import';
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('entity.dcat_source.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to import the @name DCAT feed?', [
      '@name' => $this->dcatSource->label(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, DcatSourceInterface $dcat_source = NULL) {
    $this->dcatSource = $dcat_source;
    $form_state->setStorage(['dcat_source' => $dcat_source]);
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /* @var $dcat_source \Drupal\dcat_import\Entity\DcatSourceInterface */
    $dcat_source = $form_state->getStorage()['dcat_source'];
    $id = 'dcat_import_' . $dcat_source->id() . '_dataset';

    $manager = \Drupal::service('plugin.manager.migration');
    $plugins = $manager->createInstances([$id]);
    /* @var $migration \Drupal\migrate\Plugin\MigrationInterface */
    $migration = $plugins[$id];
    $ids = $migration->get('requirements');
    $ids[] = $id;

    $batch = [
      'title' => t('Importing'),
      'operations' => [],
      'finished' => 'dcat_import_batch_import_finish',
      'file' => drupal_get_path('module', 'dcat_import') . '/dcat_import.batch_import.inc',
    ];

    foreach ($ids as $id) {
      $batch['operations'][] = ['dcat_import_batch_import', [$id]];
    }

    batch_set($batch);
  }

}
