<?php

namespace Jaxon\Attributes\Tests\TestAttributes;

use Jaxon\Attributes\Tests\AttributeTrait;
use Jaxon\Attributes\Tests\Attr\Ajax\PropertyAttribute;
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
        $xMetadata = $this->getAttributes(PropertyAttribute::class,
            ['attrVar'], ['colorService', 'fontService', 'textService']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aExcluded = $xMetadata->getExceptMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('attrVar', $aProperties);
        $this->assertCount(3, $aProperties['attrVar']['__di']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\ColorService', $aProperties['attrVar']['__di']['colorService']);
        $this->assertEquals('Jaxon\Attributes\Tests\Attr\Ajax\FontService', $aProperties['attrVar']['__di']['fontService']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\TextService', $aProperties['attrVar']['__di']['textService']);
    }

    /**
     * @throws SetupException
     */
    public function testContainerDocBlockAttribute()
    {
        $xMetadata = $this->getAttributes(PropertyAttribute::class,
            ['attrDbVar'], ['colorService', 'fontService', 'textService']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aExcluded = $xMetadata->getExceptMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('attrDbVar', $aProperties);
        $this->assertCount(3, $aProperties['attrDbVar']['__di']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\ColorService', $aProperties['attrDbVar']['__di']['colorService']);
        $this->assertEquals('Jaxon\Attributes\Tests\Attr\Ajax\FontService', $aProperties['attrDbVar']['__di']['fontService']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\TextService', $aProperties['attrDbVar']['__di']['textService']);
    }

    /**
     * @throws SetupException
     */
    public function testContainerDiAttribute()
    {
        $xMetadata = $this->getAttributes(PropertyAttribute::class,
            ['attrDi'], ['colorService1', 'fontService1', 'textService1']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aExcluded = $xMetadata->getExceptMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('*', $aProperties);
        $this->assertCount(3, $aProperties['*']['__di']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\ColorService', $aProperties['*']['__di']['colorService1']);
        $this->assertEquals('Jaxon\Attributes\Tests\Attr\Ajax\FontService', $aProperties['*']['__di']['fontService1']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\TextService', $aProperties['*']['__di']['textService1']);
    }

    /**
     * @throws SetupException
     */
    public function testContainerDiAndVarAttribute()
    {
        $xMetadata = $this->getAttributes(PropertyAttribute::class,
            ['attrDi'], ['colorService2', 'fontService2', 'textService2']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aExcluded = $xMetadata->getExceptMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('*', $aProperties);
        $this->assertCount(3, $aProperties['*']['__di']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\ColorService', $aProperties['*']['__di']['colorService2']);
        $this->assertEquals('Jaxon\Attributes\Tests\Attr\Ajax\FontService', $aProperties['*']['__di']['fontService2']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\TextService', $aProperties['*']['__di']['textService2']);
    }

    /**
     * @throws SetupException
     */
    public function testContainerPropAttribute()
    {
        $xMetadata = $this->getAttributes(PropertyAttribute::class,
            ['attrDi'], ['colorService3', 'fontService3', 'textService3']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aExcluded = $xMetadata->getExceptMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('*', $aProperties);
        $this->assertCount(3, $aProperties['*']['__di']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\ColorService', $aProperties['*']['__di']['colorService3']);
        $this->assertEquals('Jaxon\Attributes\Tests\Attr\Ajax\FontService', $aProperties['*']['__di']['fontService3']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\TextService', $aProperties['*']['__di']['textService3']);
    }

    public function testContainerAttributeErrorDiAttr()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(PropertyAttribute::class, [], ['errorDiAttr']);
    }

    public function testContainerAttributeErrorTwoDi()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(PropertyAttribute::class, [], ['errorTwoDi']);
    }

    public function testContainerAttributeErrorDiClass()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(PropertyAttribute::class, ['errorDiClass']);
    }
}
