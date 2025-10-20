<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\Attributes\Attribute\After;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Exclude;
use Jaxon\Attributes\Tests\Attr\FuncComponent;

#[Exclude(true)]
class ClassExcluded extends FuncComponent
{
    #[Exclude]
    public function doNot()
    {
    }

    #[Databag(name: 'user.name')]
    #[Databag(name: 'page.number')]
    public function withBags()
    {
    }

    #[Before(call: 'funcBefore')]
    #[After(call: 'funcAfter')]
    public function cbSingle()
    {
    }
}
