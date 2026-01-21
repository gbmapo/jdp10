<?php

declare(strict_types=1);

namespace Drupal\views_send\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Default implementation of MIME mail support availability.
 */
class ViewsSendMime implements ViewsSendMimeInterface {

  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function isAvailable(): bool {
    return $this->moduleHandler->moduleExists('mailgun')
      || $this->moduleHandler->moduleExists('mailchimp_transactional')
      || $this->moduleHandler->moduleExists('mimemail')
      || $this->moduleHandler->moduleExists('phpmailer_smtp')
      || $this->moduleHandler->moduleExists('sendgrid_integration')
      || $this->moduleHandler->moduleExists('swiftmailer')
      || $this->moduleHandler->moduleExists('symfony_mailer');
  }

  /**
   * {@inheritdoc}
   */
  public function attachMail(): bool {
    return $this->moduleHandler->moduleExists('mailchimp_transactional')
    || $this->moduleHandler->moduleExists('swiftmailer');
  }

}
