<?php

namespace Drupal\dcat_import\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DcatSourceForm.
 *
 * @package Drupal\dcat_import\Form
 */
class DcatSourceForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dcat_source = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dcat_source->label(),
      '#description' => $this->t("Label for the DCAT source."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dcat_source->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dcat_import\Entity\DcatSource::load',
      ],
      '#disabled' => !$dcat_source->isNew(),
    ];

    $form['iri'] = [
      '#type' => 'url',
      '#title' => $this->t('IRI'),
      '#maxlength' => 255,
      '#default_value' => $dcat_source->getIri(),
      '#description' => $this->t("IRI for the DCAT source."),
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $dcat_source->getDescription(),
      '#description' => $this->t("Description for the DCAT source."),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dcat_source = $this->entity;
    $status = $dcat_source->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label DCAT source.', [
          '%label' => $dcat_source->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label DCAT source.', [
          '%label' => $dcat_source->label(),
        ]));
    }
    $form_state->setRedirectUrl($dcat_source->toUrl('collection'));
  }

}
