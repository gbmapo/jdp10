<?php

namespace Drupal\views_send\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event that is fired when all email messages have been added to the spool.
 */
class AllMailAddedEvent extends Event {

  const EVENT_NAME = 'views_send_all_email_added_to_spool';

  /**
   * The message account.
   *
   * @var \Drupal\Core\TypedData\Type\IntegerInterface
   */
  public $count;

  /**
   * Constructs the object.
   *
   * @param \Drupal\Core\TypedData\Type\IntegerInterface $count
   *   The message count.
   */
  public function __construct($count) {
    $this->count = $count;
  }

}
