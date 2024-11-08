<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\hux\Attribute\Hook as HuxHook;

final class HuxHookToCoreHookRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Migrate HuxHook attribute to Core Hook attribute', [
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
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        foreach ($node->getAttrGroups() as $attributeGroup) {
            foreach ($attributeGroup->attrs as $attribute) {
                if (!$this->shouldSkip($attribute)) {
                    $attribute->name = new Node\Name('\\' . Hook::class);
                }
            }
        }

        return $node;
    }

    private function shouldSkip(Attribute $attribute): bool
    {
        if (!$this->isName($attribute, HuxHook::class)) {
            return true;
        }

        if (count($attribute->args) !== 1 || !$attribute->args[0]->value instanceof String_) {
            return true;
        }

        return false;
    }
}
