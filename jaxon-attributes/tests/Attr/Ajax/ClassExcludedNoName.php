<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\Attributes\Attribute\After;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\DataBag;
use Jaxon\Attributes\Attribute\Exclude;
use Jaxon\Attributes\Tests\Attr\FuncComponent;

#[Exclude(true)]
class ClassExcludedNoName extends FuncComponent
{
    #[Exclude]
    public function doNot()
    {
    }

    #[DataBag('user.name')]
    #[DataBag('page.number')]
    public function withBags()
    {
    }

    #[Before('funcBefore')]
    #[After('funcAfter')]
    public function cbSingle()
    {
    }
}
