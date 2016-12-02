<?php

namespace Drupal\dcat_import\Plugin\migrate\source;

use EasyRdf_Resource;

/**
 * DCAT Dataset feed source.
 *
 * @MigrateSource(
 *   id = "dcat_feed_distribution"
 * )
 */
class DistributionDcatFeedSource extends DcatFeedSource {

  /**
   * {@inheritdoc}
   */
  public function getDcatType() {
    return 'dcat:Distribution';
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
      'access_url' => t('Access URL'),
      'download_url' => t('Download URL'),
      'byte_size' => t('Byte size'),
      'format' => t('Format'),
      'license' => t('License'),
      'media_type' => t('Media type'),
      'rights' => t('Rights'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    $data = array();

    /** @var EasyRdf_Resource $distribution */
    foreach ($this->getSourceData() as $distribution) {
      $data[] = array(
        'uri' => $distribution->getUri(),
        'title' => $this->getValue($distribution, 'dc:title'),
        'description' => $this->getValue($distribution, 'dc:description'),
        'issued' => $this->getDateValue($distribution, 'dc:issued'),
        'modified' => $this->getDateValue($distribution, 'dc:modified'),
        'access_url' => $this->getValue($distribution, 'dcat:accessURL'),
        'download_url' => $this->getValue($distribution, 'dcat:downloadURL'),
        'byte_size' => $this->getValue($distribution, 'dcat:byteSize'),
        'format' => $this->getValue($distribution, 'dcat:format'),
        'license' => $this->getValue($distribution, 'dcat:license'),
        'media_type' => $this->getValue($distribution, 'dcat:mediaType'),
        'rights' => $this->getValue($distribution, 'dcat:rights'),
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
