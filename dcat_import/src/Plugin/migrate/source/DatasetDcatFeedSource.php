<?php

namespace Drupal\dcat_import\Plugin\migrate\source;

use Drupal\Component\Utility\Unicode;
use Drupal\taxonomy\Plugin\views\wizard\TaxonomyTerm;
use EasyRdf_Resource;
use Drupal\migrate\Row;

/**
 * DCAT Dataset feed source.
 *
 * @MigrateSource(
 *   id = "dcat.dataset"
 * )
 */
class DatasetDcatFeedSource extends DcatFeedSource {

  /**
   * {@inheritdoc}
   */
  public function getDcatType() {
    return 'dcat:Dataset';
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return array(
      'uri' => t('URI / ID'),
      'title' => t('Title'),
      'description' => t('Description'),
      'issued' => t('Issued'),
      'modified' => t('Modified'),
      'landing_page' => t('Landing Page'),
      'distribution' => t('Distribution'),
      'accrual_periodicity' => t('Frequency'),
      'keyword' => t('Keyword'),
      'spatial_geographical' => t('Spatial/geographical coverage'),
      'temporal' => t('Temporal'),
      'theme' => t('Theme'),
      'publisher' => t('Publisher'),
      'source' => t('Source'),
      'contact_point' => t('Contact point'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    $data = [];

    /** @var EasyRdf_Resource $dataset */
    foreach ($this->getSourceData() as $dataset) {
      $data[] = [
        'uri' => $dataset->getUri(),
        'title' => $this->getValue($dataset, 'dc:title'),
        'description' => $this->getValue($dataset, 'dc:description'),
        'issued' => $this->getDateValue($dataset, 'dc:issued'),
        'modified' => $this->getDateValue($dataset, 'dc:modified'),
        'landing_page' => $this->getValue($dataset, 'dcat:landingPage'),
        'distribution' => $this->getValue($dataset, 'dcat:distribution'),
        'accrual_periodicity' => $this->getValue($dataset, 'dc:accrualPeriodicity'),
        'keyword' => $this->getValue($dataset, 'dcat:keyword'),
        'spatial_geographical' => $this->getValue($dataset, 'dc:spatial'),
        'temporal' => $this->getValue($dataset, 'dc:temporal'),
        'theme' => $this->getValue($dataset, 'dcat:theme'),
        'publisher' => $this->getValue($dataset, 'dc:publisher'),
        'contact_point' => $this->getValue($dataset, 'dcat:contactPoint'),
      ];
    }

    return new \ArrayIterator($data);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Allow themes to be remapped.
    if (!empty($this->configuration['global_theme'])) {
      $themes = $row->getSourceProperty('theme');
      $themes = is_array($themes) ? $themes : [$themes];
      $new_themes = [];

      foreach ($themes as $theme) {
        $query = \Drupal::entityQuery('taxonomy_term')
          ->condition('vid', 'dataset_theme')
          ->condition('mapping', $theme);

        $ids = $query->execute();
        /** @var TaxonomyTerm $term */
        foreach (\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($ids) as $term) {
          $uri = $term->get('external_id')->getValue();
          $new_themes[] = $uri[0]['uri'];
        }
      }

      $row->setSourceProperty('theme', $new_themes);
    }

    if (!empty($this->configuration['lowercase_taxonomy_terms'])) {
      $terms = $row->getSourceProperty('keyword');
      $terms = is_array($terms) ? $terms : [$terms];
      $terms = array_map('strtolower', $terms);
      $row->setSourceProperty('keyword', array_unique($terms));
    }

    $keywords = [];
    foreach ($row->getSourceProperty('keyword') as $keyword) {
      $keywords[] = Unicode::truncate($keyword, 255);
    }
    array_filter($keywords);
    $row->setSourceProperty('keyword', $keywords);

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['uri']['type'] = 'string';
    return $ids;
  }

}
