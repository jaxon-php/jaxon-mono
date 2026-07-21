<?php

namespace Jaxon\Attributes\Tests\TestAttributes;

use Jaxon\Attributes\Tests\AttributeTrait;
use Jaxon\Attributes\Tests\Attr\Ajax\FontService;
use Jaxon\Attributes\Tests\Attr\Ajax\PropertyAttribute;
use Jaxon\Attributes\Tests\Service\ColorService;
use Jaxon\Attributes\Tests\Service\TextService;
use Jaxon\Exception\SetupException;
use PHPUnit\Framework\TestCase;

use function Jaxon\Attributes\_register;
use function Jaxon\jaxon;

class PropertyAttributeTest extends TestCase
{
    use AttributeTrait;

    /**
     * @var string
     */
    private $sCacheDir;

    /**
     * @throws SetupException
     */
    public function setUp(): void
    {
        $this->sCacheDir = __DIR__ . '/../cache';
        @mkdir($this->sCacheDir);

        jaxon()->di()->getPluginManager()->registerPlugins();
        _register();

        jaxon()->di()->val('jaxon_attributes_cache_dir', $this->sCacheDir);
    }

    /**
     * @throws SetupException
     */
    public function tearDown(): void
    {
        jaxon()->reset();
        parent::tearDown();
    }

    /**
     * @throws SetupException
     */
    public function testContainerAttribute()
    {
        $xMetadata = $this->getAttributes(PropertyAttribute::class, ['attrVar'],
            [PropertyAttribute::class => ['colorService', 'fontService', 'textService']]);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aExcluded = $xMetadata->getExceptMethods();

        $this->assertFalse($bExcluded);

        $di = $aProperties['attrVar']['__di'];
        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('attrVar', $aProperties);
        $this->assertCount(3, $di);
        $this->assertEquals(ColorService::class, $di['colorService'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['colorService'][1]);
        $this->assertEquals(FontService::class, $di['fontService'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['fontService'][1]);
        $this->assertEquals(TextService::class, $di['textService'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['textService'][1]);
    }

    /**
     * @throws SetupException
     */
    public function testContainerDocBlockAttribute()
    {
        $xMetadata = $this->getAttributes(PropertyAttribute::class, ['attrDbVar'],
            [PropertyAttribute::class => ['colorService', 'fontService', 'textService']]);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aExcluded = $xMetadata->getExceptMethods();

        $this->assertFalse($bExcluded);

        $di = $aProperties['attrDbVar']['__di'];
        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('attrDbVar', $aProperties);
        $this->assertCount(3, $di);
        $this->assertEquals(ColorService::class, $di['colorService'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['colorService'][1]);
        $this->assertEquals(FontService::class, $di['fontService'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['fontService'][1]);
        $this->assertEquals(TextService::class, $di['textService'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['textService'][1]);
    }

    /**
     * @throws SetupException
     */
    public function testContainerDiAttribute()
    {
        $xMetadata = $this->getAttributes(PropertyAttribute::class, ['attrDi'],
            [PropertyAttribute::class => ['colorService1', 'fontService1', 'textService1']]);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aExcluded = $xMetadata->getExceptMethods();

        $this->assertFalse($bExcluded);

        $di = $aProperties['*']['__di'];
        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('*', $aProperties);
        $this->assertCount(3, $di);
        $this->assertEquals(ColorService::class, $di['colorService1'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['colorService1'][1]);
        $this->assertEquals(FontService::class, $di['fontService1'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['fontService1'][1]);
        $this->assertEquals(TextService::class, $di['textService1'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['textService1'][1]);
    }

    /**
     * @throws SetupException
     */
    public function testContainerDiAndVarAttribute()
    {
        $xMetadata = $this->getAttributes(PropertyAttribute::class, ['attrDi'],
            [PropertyAttribute::class => ['colorService2', 'fontService2', 'textService2']]);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aExcluded = $xMetadata->getExceptMethods();

        $this->assertFalse($bExcluded);

        $di = $aProperties['*']['__di'];
        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('*', $aProperties);
        $this->assertCount(3, $di);
        $this->assertEquals(ColorService::class, $di['colorService2'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['colorService2'][1]);
        $this->assertEquals(FontService::class, $di['fontService2'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['fontService2'][1]);
        $this->assertEquals(TextService::class, $di['textService2'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['textService2'][1]);
    }

    /**
     * @throws SetupException
     */
    public function testContainerPropAttribute()
    {
        $xMetadata = $this->getAttributes(PropertyAttribute::class, ['attrDi'],
            [PropertyAttribute::class => ['colorService3', 'fontService3', 'textService3']]);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aExcluded = $xMetadata->getExceptMethods();

        $this->assertFalse($bExcluded);

        $di = $aProperties['*']['__di'];
        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('*', $aProperties);
        $this->assertCount(3, $di);
        $this->assertEquals(ColorService::class, $di['colorService3'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['colorService3'][1]);
        $this->assertEquals(FontService::class, $di['fontService3'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['fontService3'][1]);
        $this->assertEquals(TextService::class, $di['textService3'][0]);
        $this->assertEquals(PropertyAttribute::class, $di['textService3'][1]);
    }

    public function testContainerAttributeErrorDiAttr()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(PropertyAttribute::class, [], [PropertyAttribute::class => ['errorDiAttr']]);
    }

    public function testContainerAttributeErrorTwoDi()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(PropertyAttribute::class, [], [PropertyAttribute::class => ['errorTwoDi']]);
    }

    public function testContainerAttributeErrorDiClass()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(PropertyAttribute::class, ['errorDiClass']);
    }
}
