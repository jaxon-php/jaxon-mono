<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\Attributes\Attribute\After;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Attributes\Attribute\Exclude;
use Jaxon\Attributes\Attribute\Upload;
use Jaxon\Attributes\Tests\Attr\FuncComponent;
use Jaxon\Attributes\Tests\Service\ColorService;

class Attribute extends FuncComponent
{
    #[Exclude]
    public function doNot()
    {
    }

    #[Exclude(true)]
    public function doNotBool()
    {
    }

    #[Exclude('Me')]
    public function doNotError()
    {
    }

    #[Databag(name: 'user.name')]
    #[Databag(name: 'page.number')]
    public function withBags()
    {
    }

    #[Databag(mane: 'user.name')]
    #[Databag(mane: 'page.number')]
    public function withBagsError()
    {
    }

    #[Callback('jaxon.callback.first')]
    #[Callback('jaxon.callback.second')]
    public function withCallbacks()
    {
    }

    #[Upload(field: 'user-files')]
    public function saveFiles()
    {
    }

    #[Upload(name: 'user-files')]
    public function saveFilesWrongName()
    {
    }

    #[Upload(field: 'user-file1')]
    #[Upload(field: 'user-file2')]
    public function saveFilesMultiple()
    {
    }

    #[Before(call: 'funcBefore')]
    #[After(call: 'funcAfter')]
    public function cbSingle()
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

    #[Before(call: 'funcBefore1', with: ['param1'])]
    #[Before(call: 'funcBefore2', with: ['param1', 'param2'])]
    #[After(call: 'funcAfter1', with: ['param1', 'param2'])]
    public function cbParams()
    {
    }

    #[Inject(type: ColorService::class, attr: 'colorService')]
    #[Inject(type: FontService::class, attr: 'fontService')]
    public function di1()
    {
    }

    #[Inject(type: ColorService::class, attr: 'colorService')]
    #[Inject(type: '\Jaxon\Attributes\Tests\Service\TextService', attr: 'textService')]
    public function di2()
    {
    }

    #[Before(name: 'funcBefore', with: ['param1'])]
    public function cbBeforeNoCall()
    {
    }

    #[Before(call: 'funcBefore', params: ['param1'])]
    public function cbBeforeUnknownAttr()
    {
    }

    #[Before(call: 'funcBefore', with: 'param1')]
    public function cbBeforeWrongAttrType()
    {
    }

    #[After(name: 'funcAfter', with: ['param1'])]
    public function cbAfterNoCall()
    {
    }

    #[After(call: 'funcAfter', params: ['param1'])]
    public function cbAfterUnknownAttr()
    {
    }

    #[After(call: 'funcAfter', with: true)]
    public function cbAfterWrongAttrType()
    {
    }

    #[Inject(attr: 'attr', params: '')]
    public function diUnknownAttr()
    {
    }

    #[Inject(type: 'ClassName', attr: [])]
    public function diWrongAttrType()
    {
    }

    #[Inject(type: true, attr: 'attr')]
    public function diWrongClassType()
    {
    }
}
