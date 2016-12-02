<?php

namespace Drupal\dcat\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Agent entities.
 *
 * @ingroup dcat
 */
interface DcatAgentInterface extends  ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Agent name.
   *
   * @return string
   *   Name of the Agent.
   */
  public function getName();

  /**
   * Sets the Agent name.
   *
   * @param string $name
   *   The Agent name.
   *
   * @return \Drupal\dcat\Entity\DcatAgentInterface
   *   The called Agent entity.
   */
  public function setName($name);

  /**
   * Gets the Agent creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Agent.
   */
  public function getCreatedTime();

  /**
   * Sets the Agent creation timestamp.
   *
   * @param int $timestamp
   *   The Agent creation timestamp.
   *
   * @return \Drupal\dcat\Entity\DcatAgentInterface
   *   The called Agent entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Agent published status indicator.
   *
   * Unpublished Agent are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Agent is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Agent.
   *
   * @param bool $published
   *   TRUE to set this Agent to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\dcat\Entity\DcatAgentInterface
   *   The called Agent entity.
   */
  public function setPublished($published);

}
