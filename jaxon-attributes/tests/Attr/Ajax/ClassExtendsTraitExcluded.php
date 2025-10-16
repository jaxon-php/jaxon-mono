<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\Attributes\Attribute\Exclude;

class ClassExtendsTraitExcluded extends ClassExcluded
{
    use TraitAttribute;

    #[Exclude(false)]
    public function doNot()
    {
    }
}
