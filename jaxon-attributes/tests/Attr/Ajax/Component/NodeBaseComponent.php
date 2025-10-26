<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax\Component;

use Jaxon\Attributes\Attribute\Export;
use Jaxon\Attributes\Tests\Attr\NodeComponent as BaseComponent;

#[Export(base: ['render'])]
class NodeBaseComponent extends BaseComponent
{
    use Traits\NodeTrait;
}
