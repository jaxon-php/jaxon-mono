<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\App\Attribute\DI;
use Jaxon\Attributes\Tests\Attr\CallableClass;
use Jaxon\Attributes\Tests\Service\SubDir;

class SubDirImportAttribute extends CallableClass
{
    protected SubDir\FirstService $firstService;

    #[DI('SubDir\SecondService')]
    protected $secondService;

    #[DI('firstService')]
    public function attrDi()
    {
    }
}
