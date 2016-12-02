<?php

namespace Drupal\dcat_import\Plugin\migrate\source;

use EasyRdf_Resource;

/**
 * DCAT Dataset feed source.
 *
 * @MigrateSource(
 *   id = "dcat_feed_dataset"
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
    $data = array();

    /** @var EasyRdf_Resource $dataset */
    foreach ($this->getSourceData() as $dataset) {
      $data[] = array(
        'uri' => $dataset->getUri(),
        'title' => $this->getValue($dataset, 'dc:title'),
        'description' => $this->getValue($dataset, 'dc:description'),
        'issued' => $this->getDateValue($dataset, 'dc:issued'),
        'modified' => $this->getDateValue($dataset, 'dc:issued'),
        'landing_page' => $this->getValue($dataset, 'dcat:landingPage'),
        'distribution' => $this->getValue($dataset, 'dcat:distribution'),
        'accrual_periodicity' => $this->getValue($dataset, 'dc:accrualPeriodicity'),
        'keyword' => $this->getValue($dataset, 'dcat:keyword'),
        'spatial_geographical' => $this->getValue($dataset, 'dc:spatial'),
        'temporal' => $this->getValue($dataset, 'dc:temporal'),
        'theme' => $this->getValue($dataset, 'dcat:theme'),
        'publisher' => $this->getValue($dataset, 'dc:publisher'),
        'contact_point' => $this->getValue($dataset, 'dcat:contactPoint'),
      );
    }

    return new \ArrayIterator($data);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['uri']['type'] = 'string';
    return $ids;
  }

}
