<?php

declare(strict_types=1);

namespace Drupal\views_send\Service;

/**
 * MIME mail support availability.
 */
interface ViewsSendMimeInterface {

  /**
   * Detect if MIME mail support is available.
   */
  public function isAvailable(): bool;

  /**
   * Detects if MIME attachment support is available.
   */
  public function attachMail(): bool;

}
