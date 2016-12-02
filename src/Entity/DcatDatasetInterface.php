<?php

namespace Drupal\dcat\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Dataset entities.
 *
 * @ingroup dcat
 */
interface DcatDatasetInterface extends  ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Dataset name.
   *
   * @return string
   *   Name of the Dataset.
   */
  public function getName();

  /**
   * Sets the Dataset name.
   *
   * @param string $name
   *   The Dataset name.
   *
   * @return \Drupal\dcat\Entity\DcatDatasetInterface
   *   The called Dataset entity.
   */
  public function setName($name);

  /**
   * Gets the Dataset creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Dataset.
   */
  public function getCreatedTime();

  /**
   * Sets the Dataset creation timestamp.
   *
   * @param int $timestamp
   *   The Dataset creation timestamp.
   *
   * @return \Drupal\dcat\Entity\DcatDatasetInterface
   *   The called Dataset entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Dataset published status indicator.
   *
   * Unpublished Dataset are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Dataset is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Dataset.
   *
   * @param bool $published
   *   TRUE to set this Dataset to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\dcat\Entity\DcatDatasetInterface
   *   The called Dataset entity.
   */
  public function setPublished($published);

}
