<?php

namespace Drupal\node\Hooks;

use Drupal\hux\Attribute\Hook;

/**
 * Hook implementations for node.
 */
class NodeHooks {

  /**
   * Implements hook_cron().
   */
  #[Hook('cron')]
  public function cron(): void { }

  /**
   * Implements hook_theme().
   */
  #[Hook('theme', moduleName: 'system')]
  public function theme(): array {
    return [];
  }

  /**
   * Implements hook_abc().
   */
  #[Hook('abc', priority: 100)]
  public function abc(): void { }

  /**
   * Implements hook_def().
   */
  #[Hook(
    'abc',
    moduleName: 'nyancat',
    priority: 100,
  )]
  public function def(): void { }

  /**
   * Implements hook_ghi().
   */
  #[Hook('abc', 'nyancat',
    priority: 100,
  )]
  public function def(): void { }

}

?>
-----
<?php

namespace Drupal\node\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for node.
 */
class NodeHooks {

  /**
   * Implements hook_cron().
   */
  #[Hook('cron')]
  public function cron(): void { }

  /**
   * Implements hook_theme().
   */
  #[Hook('theme', module: 'system')]
  public function theme(): array {
    return [];
  }

  /**
   * Implements hook_abc().
   */
  #[Hook('abc', priority: 100)]
  public function abc(): void { }

  /**
   * Implements hook_def().
   */
  #[Hook('abc', priority: 100, module: 'nyancat')]
  public function def(): void { }

  /**
   * Implements hook_ghi().
   */
  #[Hook('abc', priority: 100, module: 'nyancat')]
  public function def(): void { }

}

?>
