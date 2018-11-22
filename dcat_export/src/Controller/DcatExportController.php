<?php

namespace Drupal\dcat_export\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\dcat_export\DcatExportService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DcatExportController.
 *
 * @package Drupal\dcat_export\Controller
 */
class DcatExportController implements ContainerInjectionInterface {

  /**
   * Config object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Current path stack object.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * DCAT export service.
   *
   * @var \Drupal\dcat_export\DcatExportService
   */
  protected $dcatExportService;

  /**
   * DcatExportController constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   The current path stack service.
   * @param \Drupal\dcat_export\DcatExportService $dcat_export_service
   *   Database service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, CurrentPathStack $current_path, DcatExportService $dcat_export_service) {
    $this->config = $config_factory->get('dcat_export.settings');
    $this->currentPath = $current_path;
    $this->dcatExportService = $dcat_export_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('path.current'),
      $container->get('dcat_export')
    );
  }

  /**
   * Export DCAT entities as serialised data.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *
   * @throws \EasyRdf_Exception
   *   Thrown if EasyRdf fails in exporting data.
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the entity type doesn't exist.
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the storage handler couldn't be loaded.
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Thrown when the output format is not found.
   */
  public function export() {
    $format = $this->getFormatFromPath($this->currentPath->getPath());

    if (!$format) {
      throw new NotFoundHttpException();
    }

    $format_info = $this->getFormatInfo($format);
    $content = $this->dcatExportService->export($format_info['easy_rdf_format']);

    $response = new Response();
    $response->headers->set('Content-Type', $format_info['content_type']);
    $response->setContent($content);

    return $response;
  }

  /**
   * Returns the chosen format based on the URL.
   *
   * @param string $path
   *   A path to extract the format from.
   *
   * @return string
   *   The output format.
   */
  protected function getFormatFromPath($path) {
    if (!substr_count($path, '.')) {
      return FALSE;
    }

    $parts = explode('.', $path);
    $format = end($parts);

    if ($this->validateFormat($format)) {
      return $format;
    }

    return FALSE;
  }

  /**
   * Get extra information about the output format.
   *
   * @param $format
   *   The output format.
   *
   * @return array|false
   *   Array containing the Internet Media Type and the EasyRdf format or false
   *   when not found.
   */
  protected function getFormatInfo($format) {
    switch ($format) {
      case 'xml':
        $easy_rdf_format = 'rdfxml';
        $content_type = 'text/xml';
        break;

      case 'ttl':
        $easy_rdf_format = 'turtle';
        $content_type = 'text/turtle';
        break;

      case 'json':
        $easy_rdf_format = 'json';
        $content_type = 'application/json';
        break;

      case 'jsonld':
        $easy_rdf_format = 'jsonld';
        $content_type = 'application/ld+json';
        break;

      case 'nt':
        $easy_rdf_format = 'ntriples';
        $content_type = 'application/n-triples';
        break;

      default:
        return FALSE;
    }

    return [
      'easy_rdf_format' => $easy_rdf_format,
      'content_type' => $content_type,
    ];
  }

  /**
   * Check whether or not the format exists and is activated.
   *
   * @return bool
   *   True if the format exists and is activated.
   */
  protected function validateFormat($format) {
    return in_array($format, array_filter($this->config->get('formats')));
  }

}
