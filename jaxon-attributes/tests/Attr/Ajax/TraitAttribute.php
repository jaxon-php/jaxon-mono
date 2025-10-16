<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\Attributes\Attribute\After;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;

trait TraitAttribute
{
    #[Callback('jaxon.callback.first')]
    #[Callback('jaxon.callback.second')]
    public function withCallbacks()
    {
    }

    #[Before(call: 'funcBefore1')]
    #[Before(call: 'funcBefore2')]
    #[After(call: 'funcAfter1')]
    #[After(call: 'funcAfter2')]
    #[After(call: 'funcAfter3')]
    public function cbMultiple()
    {
    }
}
