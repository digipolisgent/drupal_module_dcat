<?php

namespace Drupal\dcat_import;

use Drupal\migrate\MigrateMessageInterface;

class DcatImportLogMigrateMessage implements MigrateMessageInterface {

  /**
   * Output a message from the migration.
   *
   * @param string $message
   *   The message to display.
   * @param string $type
   *   The type of message to display.
   *
   * @see drush_log()
   */
  public function display($message, $type = 'status') {
    drupal_set_message($message, $type);
  }

}
