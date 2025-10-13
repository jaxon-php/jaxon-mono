<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Attributes\Tests\Attr\FuncComponent;
use Jaxon\Attributes\Tests\Service\SubDir;

class SubDirImportAttribute extends FuncComponent
{
    protected SubDir\FirstService $firstService;

    #[Inject(SubDir\SecondService::class)]
    protected $secondService;

    #[Inject(attr: 'firstService')]
    public function attrDi()
    {
    }
}
