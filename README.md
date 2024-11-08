# HuxHookToCoreHookRector

Converts Hook attributes from Hux module to Drupal core.

## Status

 * [x] `\Drupal\hux\Attribute\Hook` to `\Drupal\Core\Attribute\Hook`
 * [ ] `\Drupal\hux\Attribute\Alter`
 * [ ] Other hux attributes

Core expects hooks to exist in the `Hook` namespace, but hux expects `Hooks`.
The rector does not yet update the namespace. If hux is still required, such as
to support Alter hooks, hux will need to be configured to look at the core
namespace.

## Usage

Quick and dirty, clone to your project root and add this to composer.json

```json
{
  "autoload-dev": {
    "psr-4": {
      "Utils\\Rector\\": "utils/rector/src",
      "Utils\\Rector\\Tests\\": "utils/rector/tests"
    }
  }
}
```

Then run `composer dump-autoload`.

Create a rector.php file as below:

```php
<?php

use Rector\Config\RectorConfig;
use Utils\Rector\Rector\HuxHookToCoreHookRector;

return RectorConfig::configure()
  ->withPaths([
    __DIR__ . '/path/to/your/module',
  ])
  ->withRules([
    HuxHookToCoreHookRector::class,
  ])
  ->withImportNames(
    importDocBlockNames: false,
    importShortClasses: false,
  );
```

Finally, run `vendor/bin/rector process`.
