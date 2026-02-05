<?php

namespace Drupal\views_send\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event that is fired when an email message has been added to the spool.
 */
class MailAddedEvent extends Event {

  const EVENT_NAME = 'views_send_email_added_to_spool';

  /**
   * Mail subject.
   *
   * @var string
   */
  protected $subject;

  /**
   * Constructs the mail object.
   *
   * @param array|object $message
   *   The mail message.
   */
  public function __construct($message) {
    if (is_array($message)) {
      $message = (object) $message;
    }
    $this->subject = $message->subject;
  }

  /**
   * Gets the subject of the mail.
   *
   * @return string
   *   The mail subject.
   */
  public function getSubject() {
    return $this->subject;
  }

}
