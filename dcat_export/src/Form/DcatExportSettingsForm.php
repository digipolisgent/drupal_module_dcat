<?php

namespace Drupal\dcat_export\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure dcat export settings for this site.
 */
class DcatExportSettingsForm extends ConfigFormBase {

  /**
   * RouteBuilder object.
   *
   * @var \Drupal\Core\Routing\RouteBuilderInterface
   */
  protected $routeBuilder;

  /**
   * Class constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RouteBuilderInterface $route_builder) {
    parent::__construct($config_factory);
    $this->routeBuilder = $route_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('router.builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dcat_export_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dcat_export.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dcat_export.settings');

    $form['#tree'] = FALSE;

    $form['general'] = [
      '#type' => 'details',
      '#title' => t('General settings'),
      '#open' => TRUE,
    ];

    $general = &$form['general'];

    $general['formats'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Output formats'),
      '#description' => $this->t('Append the extension of the selected output formats to the /dcat path.'),
      '#default_value' => $config->get('formats'),
      '#multiple' => TRUE,
      '#options' => [
        'xml' => 'XML (.xml)',
        'ttl' => 'Turtle (.ttl)',
        'json' => 'JSON (.json)',
        'jsonld' => 'JSON-LD (.jsonld)',
        'nt' => 'N-Tripples (.nt)',
      ],
      '#required' => TRUE,
    ];

    $general['endpoints'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Available endpoints'),
    ];

    $general['endpoints']['list'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => $this->getEndpointLinks(),
    ];

    $form['catalog'] = [
      '#type' => 'details',
      '#title' => t('Catalog settings'),
      '#description' => t('Configure the general catalog properties. This data will be visible in the DCAT feed. A data catalog is a curated collection of metadata about datasets.'),
      '#open' => TRUE,
    ];

    $cat = &$form['catalog'];

    $cat['catalog_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 255,
      '#default_value' => $config->get('catalog_title'),
      '#description' => $this->t('The title of the DCAT feed.'),
      '#required' => TRUE,
    ];

    $cat['catalog_description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $config->get('catalog_description'),
      '#description' => $this->t('The description of the DCAT feed.'),
      '#required' => TRUE,
    ];

    $cat['catalog_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URI'),
      '#default_value' => $config->get('catalog_uri'),
      '#description' => $this->t('The URI of the catalog.'),
      '#required' => TRUE,
    ];

    $cat['catalog_language_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Language URI'),
      '#default_value' => $config->get('catalog_language_uri'),
      '#description' => $this->t('The URI of the catalog language.'),
      '#required' => TRUE,
    ];

    $cat['catalog_homepage_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Homepage'),
      '#default_value' => $config->get('catalog_homepage_uri'),
      '#description' => $this->t('The homepage of the DCAT feed.'),
      '#required' => TRUE,
    ];

    $cat['catalog_issued'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Issued date'),
      '#default_value' => new DrupalDateTime($config->get('catalog_issued')),
      '#description' => $this->t('The date when this feed was first issued.'),
      '#required' => TRUE,
    ];

    $cat['catalog_publisher_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Publisher URI'),
      '#default_value' => $config->get('catalog_publisher_uri'),
      '#description' => $this->t('The uri of the publisher of this feed.'),
      '#required' => TRUE,
    ];

    $cat['catalog_publisher_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Publisher name'),
      '#default_value' => $config->get('catalog_publisher_name'),
      '#description' => $this->t('The name of the publisher of this feed.'),
      '#required' => TRUE,
    ];

    $cat['catalog_license_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('License URI'),
      '#default_value' => $config->get('catalog_license_uri'),
      '#description' => $this->t('This links to the license document under which the catalog is made available and not the datasets. Even if the license of the catalog applies to all of its datasets and distributions, it should be replicated on each distribution.'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('dcat_export.settings');

    foreach ($form_state->getvalues() as $key => $value) {
      if ($key === 'catalog_issued') {
        $value = (string) $value;
      }

      $config->set($key, $value);
    }

    $config->save();
    $this->routeBuilder->rebuild();

    parent::submitForm($form, $form_state);
  }

  /**
   * Get an array of available endpoint links.
   *
   * @return array
   *   Array containing endpoint links.
   */
  protected function getEndpointLinks() {
    $config = $this->config('dcat_export.settings');
    $export_paths = [];

    foreach (array_filter($config->get('formats')) as $format) {
      $url = Url::fromRoute('dcat_export.export.' . $format, [], ['absolute' => TRUE])->toString();
      $export_paths[] = Link::createFromRoute($url, 'dcat_export.export.' . $format);
    }

    return $export_paths;
  }

}
