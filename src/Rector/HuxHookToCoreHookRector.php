<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\hux\Attribute\Hook as HuxHook;

final class HuxHookToCoreHookRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Migrate Hux Hook attribute to Core Hook attribute', [
            new CodeSample(
                <<<'CODE_SAMPLE'
#[\Drupal\hux\Attribute\Hook('cron')]
public function cron(): void { }
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
#[\Drupal\Core\Hook\Attribute\Hook('cron')]
public function cron(): void { }
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [
          Node\Stmt\Use_::class,
          Node\Stmt\ClassMethod::class,
          Node\Stmt\Namespace_::class,
        ];
    }

    /**
     * @param Node\Stmt\ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
      if ($node instanceof Node\Stmt\ClassMethod) {
        $changed = false;
        foreach ($node->getAttrGroups() as $attributeGroup) {
          foreach ($attributeGroup->attrs as $attribute) {
            if (!$this->shouldSkip($attribute)) {
              $changed = true;
              $this->refactorAttribute($attribute);
            }
          }
        }
        if ($changed) {
          return $node;
        }
        return null;
      }

      if ($node instanceof Node\Stmt\Use_) {
        foreach ($node->uses as $use) {
          if ($this->isName($use, HuxHook::class)) {
            $use->name = new Node\Name(Hook::class);
            return $node;
          }
        }
      }

      if ($node instanceof Node\Stmt\Namespace_) {
        $parts = $node->name->getParts();
        if (count($parts) === 3 && $parts[0] === 'Drupal' && $parts[2] === 'Hooks') {
          $node->name = new Node\Name(['Drupal', $parts[1], 'Hook']);
          return $node;
        }

        return null;
      }

      return null;
    }

    private function refactorAttribute(Attribute $attribute): void
    {
        $isFullyQualified = $attribute->name->getAttribute('originalName') instanceof Node\Name\FullyQualified;
        $attribute->name = $isFullyQualified
         ? new Node\Name\FullyQualified(Hook::class)
         : new Node\Name('Hook');
        foreach ($attribute->args as $position => $arg) {
          $name = $arg->name;
          if ($name === null) {
            $name = match ($position) {
              0 => 'hook',
              1 => 'moduleName',
              2 => 'priority',
              default => throw new \Exception('Invalid argument position'),
            };
            $arg->name = new Node\Identifier($name);
          }
        }
      $newArgsOrder = ['hook', 'method', 'priority', 'moduleName'];
      usort($attribute->args, function ($a, $b) use ($newArgsOrder) {
        return array_search($a->name, $newArgsOrder) <=> array_search($b->name, $newArgsOrder);
      });
      foreach ($attribute->args as $arg) {
        $arg->name = match ($arg->name->name) {
          'hook' => null,
          'moduleName' => new Node\Identifier('module'),
          'priority' => new Node\Identifier('priority'),
          default => throw new \Exception('Invalid argument name'),
        };
      }
    }

    private function shouldSkip(Attribute $attribute): bool
    {
        if (!$this->isName($attribute, HuxHook::class)) {
            return true;
        }

        return false;
    }
}
