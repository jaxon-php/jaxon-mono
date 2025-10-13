<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\Attributes\Attribute\After;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\DataBag;
use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Attributes\Attribute\Exclude;
use Jaxon\Attributes\Tests\Attr\FuncComponent;
use Jaxon\Attributes\Tests\Service\TextService;

#[Exclude(false)]
#[Databag('user.name')]
#[Databag('page.number')]
#[Before('funcBefore1')]
#[Before('funcBefore2')]
#[After('funcAfter1')]
#[After('funcAfter2')]
#[After('funcAfter3')]
#[Inject('\Jaxon\Attributes\Tests\Service\ColorService', 'colorService')]
#[Inject(TextService::class, 'textService')]
#[Inject(FontService::class, 'fontService')]
class ClassAttributeNoName extends FuncComponent
{
}
