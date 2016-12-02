<?php

namespace Drupal\dcat_import\Plugin\migrate\source;

use EasyRdf_Resource;
use EasyRdf_Graph;

/**
 * Agent feed source.
 *
 * @MigrateSource(
 *   id = "dcat_feed_vcard"
 * )
 */
class VcardDcatFeedSource extends DcatFeedSource {

  /**
   * Not in use.
   *
   * @see getDcatData()
   */
  public function getDcatType() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getDcatData(EasyRdf_Graph $graph) {
    $vcards = array();
    $datasets = $graph->allOfType('dcat:Dataset');

    /** @var EasyRdf_Resource $dataset */
    foreach ($datasets as $dataset) {
      $vcards = array_merge($vcards, $dataset->allResources('dcat:contactPoint'));
    }

    // Remove duplicates.
    $uris = array();
    /** @var EasyRdf_Resource $vcard */
    foreach ($vcards as $key => $vcard) {
      $uri = $vcard->getUri();
      if (isset($uris[$uri])) {
        unset($vcards[$key]);
      }
      else {
        $uris[$uri] = $uri;
      }
    }

    return $vcards;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return array(
      'uri' => t('URI / ID'),
      'name' => t('Name'),
      'email' => t('Email'),
      'telephone' => t('Telephone'),
      'country' => t('Country'),
      'locality' => t('Locality'),
      'postal_code' => t('Postal code'),
      'region' => t('Region'),
      'street_address' => t('Street address'),
      'nickname' => t('Nickname'),
      'type' => t('Type'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    $bundle_mapping = array(
      'vcard:Organization' => 'organization',
      'vcard:Individual' => 'individual',
      'vcard:Location' => 'location',
    );

    $data = array();

    /** @var EasyRdf_Resource $agent */
    foreach ($this->getSourceData() as $agent) {
      if (isset($bundle_mapping[$agent->type()])) {
        $bundle = $bundle_mapping[$agent->type()];
      }
      else {
        // Default to organization;
        $bundle = 'organization';
      }

      $data[] = array(
        'uri' => $agent->getUri(),
        'type' => $bundle,
        'name' => $this->getValue($agent, 'vcard:fn'),
        'email' => $this->getValue($agent, 'vcard:hasEmail'),
        'telephone' => $this->getValue($agent, 'vcard:hasTelephone'),
        'country' => $this->getValue($agent, 'vcard:country-name'),
        'locality' => $this->getValue($agent, 'vcard:locality'),
        'postal_code' => $this->getValue($agent, 'vcard:postal-code'),
        'region' => $this->getValue($agent, 'vcard:region'),
        'street_address' => $this->getValue($agent, 'vcard:street-address'),
        'nickname' => $this->getValue($agent, 'vcard:hasNickname'),
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
