<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\Attributes\Attribute\After;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\DataBag;
use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Attributes\Attribute\Exclude;
use Jaxon\Attributes\Attribute\Upload;
use Jaxon\Attributes\Tests\Attr\FuncComponent;
use Jaxon\Attributes\Tests\Service\ColorService;

class AttributeNoName extends FuncComponent
{
    #[Exclude(true)]
    public function doNot()
    {
    }

    #[Exclude('Me')]
    public function doNotError()
    {
    }

    #[Databag('user.name')]
    #[Databag('page.number')]
    public function withBags()
    {
    }

    #[Databag('user:name')]
    public function withBagsErrorName()
    {
    }

    #[Databag('page number')]
    public function withBagsErrorNumber()
    {
    }

    #[Upload('user-files')]
    #[Exclude(false)]
    public function saveFiles()
    {
    }

    #[Upload('user:file')]
    public function saveFileErrorFieldName()
    {
    }

    #[Upload('user file')]
    public function saveFileErrorFieldNumber()
    {
    }

    #[Upload('user-files')]
    public function saveFilesWrongName()
    {
    }

    #[Upload('user-file1')]
    #[Upload('user-file2')]
    public function saveFilesMultiple()
    {
    }

    #[Before('funcBefore')]
    #[After('funcAfter')]
    public function cbSingle()
    {
    }

    #[Before('funcBefore1')]
    #[Before('funcBefore2')]
    #[After('funcAfter1')]
    #[After('funcAfter2')]
    #[After('funcAfter3')]
    public function cbMultiple()
    {
    }

    #[Before('funcBefore1', ["param1"])]
    #[Before('funcBefore2', ["param1", "param2"])]
    #[After('funcAfter1', ["param1", "param2"])]
    public function cbParams()
    {
    }

    #[Before('func:Before')]
    public function cbBeforeErrorName()
    {
    }

    #[Before('funcBefore', false)]
    public function cbBeforeErrorParam()
    {
    }

    #[Before('func:After')]
    public function cbAfterErrorName()
    {
    }

    #[Before('funcAfter', false)]
    public function cbAfterErrorParam()
    {
    }

    #[Inject(ColorService::class, 'colorService')]
    #[Inject(FontService::class, 'fontService')]
    public function di1()
    {
    }

    #[Inject(ColorService::class, 'colorService')]
    #[Inject('\Jaxon\Attributes\Tests\Service\TextService', 'textService')]
    public function di2()
    {
    }

    #[Inject(ColorService::class, 'color.Service')]
    public function diErrorAttr()
    {
    }

    #[Inject('Color.Service', 'colorService')]
    public function diErrorClass()
    {
    }

    #[Inject('colorService')]
    public function diErrorOneParam()
    {
    }
}
