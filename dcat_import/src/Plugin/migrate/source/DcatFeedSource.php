<?php

namespace Drupal\dcat_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use EasyRdf_Graph;
use EasyRdf_Resource;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\dcat_import\Plugin\DcatGraph;

/**
 * DCAT feed source.
 */
abstract class DcatFeedSource extends SourcePluginBase {

  /**
   * Array of source data.
   *
   * @var array.
   */
  private $sourceData;

  /**
   * Bool to indicate if the extractor has already ran.
   *
   * @var bool
   */
  private $extractionDone = FALSE;

  /**
   * Returns the DCAT type to extract from the feed.
   *
   * E.g. dcat:Dataset, dcat:Distribution ...
   *
   * @return string
   *   The DCAT type to extract.
   */
  public abstract function getDcatType();

  /**
   * Extract data from the given EasyRdf Graph.
   *
   * @param EasyRdf_Graph $graph
   *   The EasyRdf Graph to extract the data from.
   *
   * @return array
   *   The extracted data.
   */
  public function getDcatData(EasyRdf_Graph $graph) {
    return $graph->allOfType($this->getDcatType());
  }

  /**
   * Data getter.
   *
   * @return array
   *   Array of source data.
   */
  public function getSourceData() {
    if (!$this->extractionDone) {
      $format = isset($this->configuration['format']) ? $this->configuration['format'] : 'turtle';
      $pager_argument = isset($this->configuration['pager_argument']) ? $this->configuration['pager_argument'] : NULL;

      $graph = DcatGraph::newAndLoad($this->configuration['uri'], $format, $pager_argument);
      $this->sourceData = $this->getDcatData($graph);

      $this->extractionDone = TRUE;
    }

    return $this->sourceData;
  }

  /**
   * Allows class to decide how it will react when it is treated like a string.
   */
  public function __toString() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    return count($this->getSourceData());
  }

  /**
   * Unify the return values.
   *
   * @param mixed $value
   *   The value to unify.
   *
   * @return null|string|array
   *   Null if empty, string if single value, array if multi value.
   */
  public function unifyReturnValue($value) {
    if (empty($value)) {
      return NULL;
    }
    if (!is_array($value)) {
      return $value;
    }
    if (count($value) == 1) {
      return array_shift($value);
    }

    return $value;
  }

  /**
   * Return all values for a property from an EasyRdf resource.
   *
   * @param \EasyRdf_Resource $resource
   *   The EasyRdf resource to get the property from.
   * @param string $property
   *   The name of the property to get.
   *
   * @return array
   *   The values as an array of strings.
   */
  public function getValueArray(EasyRdf_Resource $resource, $property) {
    $values = array();

    foreach ($resource->all($property) as $value) {
      if (!empty($value)) {
        $values[] = $this->getSingleValue($value);
      }
    }

    return $values;
  }

  /**
   * Convert an EasyRdf Resource or Literal to a single value.
   *
   * @param mixed $value
   *   EasyRdf_Resource or EasyRdf_Literal object.
   *
   * @return string
   *   A single value representing the object.
   */
  public function getSingleValue($value) {
    $class = get_class($value);
    switch ($class) {
      case 'EasyRdf_Resource':
        return $value->getUri();

      case 'EasyRdf_Literal_DateTime':
        return $value->getValue()->format('c');

      default:
        return $value->getValue();
    }
  }

  /**
   * Get the value for a property from an EasyRdf resource.
   *
   * @param \EasyRdf_Resource $resource
   *   The EasyRdf resource to get the property from.
   * @param string $property
   *   The name of the property to get.
   *
   * @return null|string|array
   *   Null if empty, string if single value, array if multi value.
   */
  public function getValue(EasyRdf_Resource $resource, $property) {
    $values = $this->getValueArray($resource, $property);
    return $this->unifyReturnValue($values);
  }

  /**
   * Get a certain property from an EasyRdf resource as datetime storage string.
   *
   * @param \EasyRdf_Resource $resource
   *   The EasyRdf resource to get the property from.
   * @param string $property
   *   The name of the property to get.
   *
   * @return null|string|array
   *   Null if empty, string if single value, array if multi value.
   */
  public function getDateValue(EasyRdf_Resource $resource, $property) {
    $values = $this->getValueArray($resource, $property);

    $dates = array();
    foreach ($values as $value) {
      $date = new DrupalDateTime($value);
      $dates[] = $date->format(DATETIME_DATETIME_STORAGE_FORMAT);
    }

    return $this->unifyReturnValue($dates);
  }

  /**
   * Get a certain property from an EasyRdf resource as an email string.
   *
   * Basically removes mailto: part.
   *
   * @param \EasyRdf_Resource $resource
   *   The EasyRdf resource to get the property from.
   * @param string $property
   *   The name of the property to get.
   *
   * @return null|string|array
   *   Null if empty, string if single value, array if multi value.
   */
  public function getEmailValue(EasyRdf_Resource $resource, $property) {
    $values = $this->getValueArray($resource, $property);

    $emails = [];
    foreach ($values as $value) {
      $emails[] = $this->stripMailto($value);
    }

    return $this->unifyReturnValue($emails);
  }

  /**
   * Strip mailto: at the start of the given value.
   *
   * @param string $value
   *   The value to strip mailto: from e.g. mailto:me@example.com.
   *
   * @return string
   *   The value without the mailto: part e.g. me@example.com.
   */
  public function stripMailto($value) {
    return preg_replace("/^mailto:/", '', $value);
  }

}