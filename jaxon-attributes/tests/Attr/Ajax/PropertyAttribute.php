<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax;

use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Attributes\Tests\Attr\FuncComponent;
use Jaxon\Attributes\Tests\Service\ColorService;

class PropertyAttribute extends FuncComponent
{
    protected ColorService $colorService;

    protected FontService $fontService;

    protected \Jaxon\Attributes\Tests\Service\TextService $textService;

    #[Inject(ColorService::class)]
    protected $colorService1;

    #[Inject(FontService::class)]
    protected $fontService1;

    #[Inject('\Jaxon\Attributes\Tests\Service\TextService')]
    protected $textService1;

    #[Inject]
    protected ColorService $colorService2;

    #[Inject]
    protected FontService $fontService2;

    #[Inject]
    protected \Jaxon\Attributes\Tests\Service\TextService $textService2;

    #[Inject(type: ColorService::class)]
    protected $colorService3;

    #[Inject(type: FontService::class)]
    protected $fontService3;

    #[Inject(type: '\Jaxon\Attributes\Tests\Service\TextService')]
    protected $textService3;

    #[Inject(FontService::class, 'fontService')]
    protected $errorTwoParams;

    #[Inject(attr: 'fontService')]
    protected $errorDiAttr;

    #[Inject]
    #[Inject(FontService::class)]
    protected $errorTwoDi;

    #[Inject(attr: 'colorService')]
    #[Inject(attr: 'fontService')]
    #[Inject(attr: 'textService')]
    public function attrVar()
    {
    }

    #[Inject(attr: 'colorService')]
    #[Inject(attr: 'fontService')]
    #[Inject(attr: 'textService')]
    public function attrDbVar()
    {
    }

    public function attrDi()
    {
    }

    #[Inject(type: ColorService::class)]
    public function errorDiClass()
    {
    }
}
