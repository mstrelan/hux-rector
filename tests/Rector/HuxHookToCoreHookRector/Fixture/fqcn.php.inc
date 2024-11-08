<?php

namespace Rector\Tests\TypeDeclaration\Rector\HuxHookToCoreHookRector\Fixture;

/**
 * Hook implementations for node.
 */
class NodeHooks {

  /**
   * Implements hook_cron().
   */
  #[\Drupal\hux\Attribute\Hook('cron')]
  public function cron(): void { }

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\HuxHookToCoreHookRector\Fixture;

/**
 * Hook implementations for node.
 */
class NodeHooks {

  /**
   * Implements hook_cron().
   */
  #[\Drupal\Core\Hook\Attribute\Hook('cron')]
  public function cron(): void { }

}

?>
