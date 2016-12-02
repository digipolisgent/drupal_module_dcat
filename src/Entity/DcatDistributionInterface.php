<?php

namespace Drupal\dcat\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Distribution entities.
 *
 * @ingroup dcat
 */
interface DcatDistributionInterface extends  ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Distribution name.
   *
   * @return string
   *   Name of the Distribution.
   */
  public function getName();

  /**
   * Sets the Distribution name.
   *
   * @param string $name
   *   The Distribution name.
   *
   * @return \Drupal\dcat\Entity\DcatDistributionInterface
   *   The called Distribution entity.
   */
  public function setName($name);

  /**
   * Gets the Distribution creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Distribution.
   */
  public function getCreatedTime();

  /**
   * Sets the Distribution creation timestamp.
   *
   * @param int $timestamp
   *   The Distribution creation timestamp.
   *
   * @return \Drupal\dcat\Entity\DcatDistributionInterface
   *   The called Distribution entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Distribution published status indicator.
   *
   * Unpublished Distribution are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Distribution is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Distribution.
   *
   * @param bool $published
   *   TRUE to set this Distribution to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\dcat\Entity\DcatDistributionInterface
   *   The called Distribution entity.
   */
  public function setPublished($published);

}
