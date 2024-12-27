<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\App\Attribute\After;
use Jaxon\App\Attribute\Before;
use Jaxon\App\Attribute\DataBag;
use Jaxon\App\Attribute\Exclude;
use Jaxon\Attributes\Tests\Attr\CallableClass;

#[Exclude(true)]
class ClassExcluded extends CallableClass
{
    #[Exclude]
    public function doNot()
    {
    }

    #[DataBag(name: 'user.name')]
    #[DataBag(name: 'page.number')]
    public function withBags()
    {
    }

    #[Before(call: 'funcBefore')]
    #[After(call: 'funcAfter')]
    public function cbSingle()
    {
    }
}
