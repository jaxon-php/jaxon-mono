<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\Attributes\Attribute\After;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\DataBag;
use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Attributes\Attribute\Exclude;
use Jaxon\Attributes\Tests\Attr\FuncComponent;
use Jaxon\Attributes\Tests\Service\TextService;

#[Exclude(false)]
#[Databag(name: 'user.name')]
#[Databag(name: 'page.number')]
#[Before(call: 'funcBefore1')]
#[Before(call: 'funcBefore2')]
#[After(call: 'funcAfter1')]
#[After(call: 'funcAfter2')]
#[After(call: 'funcAfter3')]
#[Inject(type: '\Jaxon\Attributes\Tests\Service\ColorService', attr: 'colorService')]
#[Inject(type: TextService::class, attr: 'textService')]
#[Inject(type: FontService::class, attr: 'fontService')]
#[Callback('jaxon.callback.first')]
#[Callback('jaxon.callback.second')]
class ClassAttribute extends FuncComponent
{
}
