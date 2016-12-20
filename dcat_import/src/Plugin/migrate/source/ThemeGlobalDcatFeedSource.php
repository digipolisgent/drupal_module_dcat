<?php

namespace Drupal\dcat_import\Plugin\migrate\source;

use EasyRdf_Resource;
use EasyRdf_Graph;

/**
 * External theme feed source.
 *
 * @MigrateSource(
 *   id = "dcat.global_theme"
 * )
 */
class ThemeGlobalDcatFeedSource extends DcatFeedSource {

  /**
   * Not in use.
   *
   * @see getDcatData()
   */
  public function getDcatType() {
    return 'skos:Concept';
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return array(
      'uri' => t('URI / ID'),
      'name' => t('Name'),
      'description' => t('Description'),
      'mapping' => t('Mapping'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    $data = array();

    /** @var EasyRdf_Resource $theme */
    foreach ($this->getSourceData() as $theme) {

      $data[] = array(
        'uri' => $theme->getUri(),
        'name' => $this->getValue($theme, 'skos:prefLabel'),
        'description' => $this->getValue($theme, 'rdfs:comment'),
        'mapping' => $this->getMappingValues($theme),
      );
    }

    return new \ArrayIterator($data);
  }

  /**
   * Returns the mapping field values for the given $theme.
   *
   * @param EasyRdf_Resource $theme
   *   The resource to get the mapping values from.
   *
   * @return array|null|string
   *   The mapping values.
   */
  public function getMappingValues(EasyRdf_Resource $theme) {
    $mapping = $this->getValue($theme, 'skos:exactMatch');
    if (empty($mapping)) {
      $mapping = $this->getValue($theme, 'skos:broadMatch');
    }
    return $mapping;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['uri']['type'] = 'string';
    return $ids;
  }

}
