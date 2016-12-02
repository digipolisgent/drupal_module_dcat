<?php

namespace Drupal\dcat_import\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining DCAT source entities.
 */
interface DcatSourceInterface extends ConfigEntityInterface {

  /**
   * Returns the DCAT source IRI.
   *
   * @return string
   *   The DCAT source IRI.
   */
  public function getIri();

  /**
   * Sets the IRI of the DCAT source.
   *
   * @param string $iri
   *   The new IRI.
   *
   * @return $this
   */
  public function setIri($iri);

  /**
   * Returns the DCAT source description.
   *
   * @return string
   *   The DCAT source description.
   */
  public function getDescription();

  /**
   * Sets the description of the DCAT source.
   *
   * @param string $description
   *   The new description.
   *
   * @return $this
   */
  public function setDescription($description);

}
