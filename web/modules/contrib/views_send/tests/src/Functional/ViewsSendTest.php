<?php

declare(strict_types=1);

namespace Drupal\Tests\views_send\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Test Views Send.
 */
#[Group('views_send')]
final class ViewsSendTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'claro';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['views_send'];

  /**
   * Test callback.
   */
  public function testInstall(): void {
    $admin_user = $this->drupalCreateUser(['administer views_send']);
    $this->drupalLogin($admin_user);
    $this->drupalGet(Url::fromRoute('views_send.configure'));
    $this->assertSession()->elementExists('xpath', '//h1[text() = "Views Send Configuration"]');
  }

}
