<?php

namespace Drupal\dcat_export\Routing;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines dynamic routes.
 */
class ExportRoutes implements ContainerInjectionInterface {

  /**
   * Config object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs an ExportRoutes object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('dcat_export.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'));
  }

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $route_collection = new RouteCollection();
    $formats = array_filter($this->config->get('formats'));

    foreach ($formats as $format) {
      $route = new Route(
        '/dcat.' . $format,
        [
          '_controller' => '\Drupal\dcat_export\Controller\DcatExportController::export',
          '_title' => 'DCAT feed'
        ],
        [
          '_permission'  => 'access dcat export feed',
        ]
      );

      $route_collection->add('dcat_export.export.' . $format, $route);
    }

    return $route_collection;
  }

}
