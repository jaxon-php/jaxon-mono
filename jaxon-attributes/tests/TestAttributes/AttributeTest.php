<?php

namespace Jaxon\Attributes\Tests\TestAttributes;

use Jaxon\Attributes\Tests\AttributeTrait;
use Jaxon\Attributes\Tests\Attr\Ajax\Attribute;
use Jaxon\Attributes\Tests\Attr\Ajax\ClassAttribute;
use Jaxon\Attributes\Tests\Attr\Ajax\ClassExcluded;
use Jaxon\Exception\SetupException;
use PHPUnit\Framework\TestCase;

use function Jaxon\Attributes\_register;
use function Jaxon\jaxon;

class AttributeTest extends TestCase
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
    public function testUploadAndExcludeAttribute()
    {
        $xMetadata = $this->getAttributes(Attribute::class, ['saveFiles', 'doNot']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(1, $aProtected);
        $this->assertEquals('doNot', $aProtected[0]);

        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('saveFiles', $aProperties);
        $this->assertCount(1, $aProperties['saveFiles']);
        $this->assertEquals("'user-files'", $aProperties['saveFiles']['upload']);
    }

    /**
     * @throws SetupException
     */
    public function testDataBagAttribute()
    {
        $xMetadata = $this->getAttributes(Attribute::class, ['withBags']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('withBags', $aProperties);
        $this->assertCount(1, $aProperties['withBags']);
        $this->assertCount(2, $aProperties['withBags']['bags']);
        $this->assertEquals('user.name', $aProperties['withBags']['bags'][0]);
        $this->assertEquals('page.number', $aProperties['withBags']['bags'][1]);
    }

    /**
     * @throws SetupException
     */
    public function testCallbacksAttribute()
    {
        $xMetadata = $this->getAttributes(Attribute::class, ['cbSingle', 'cbMultiple', 'cbParams']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(3, $aProperties);
        $this->assertArrayHasKey('cbSingle', $aProperties);
        $this->assertArrayHasKey('cbMultiple', $aProperties);
        $this->assertArrayHasKey('cbParams', $aProperties);

        $this->assertCount(1, $aProperties['cbSingle']['__before']);
        $this->assertCount(2, $aProperties['cbMultiple']['__before']);
        $this->assertCount(2, $aProperties['cbParams']['__before']);
        $this->assertArrayHasKey('funcBefore', $aProperties['cbSingle']['__before']);
        $this->assertArrayHasKey('funcBefore1', $aProperties['cbMultiple']['__before']);
        $this->assertArrayHasKey('funcBefore2', $aProperties['cbMultiple']['__before']);
        $this->assertArrayHasKey('funcBefore1', $aProperties['cbParams']['__before']);
        $this->assertArrayHasKey('funcBefore2', $aProperties['cbParams']['__before']);
        $this->assertIsArray($aProperties['cbSingle']['__before']['funcBefore']);
        $this->assertIsArray($aProperties['cbMultiple']['__before']['funcBefore1']);
        $this->assertIsArray($aProperties['cbMultiple']['__before']['funcBefore2']);
        $this->assertIsArray($aProperties['cbParams']['__before']['funcBefore1']);
        $this->assertIsArray($aProperties['cbParams']['__before']['funcBefore2']);

        $this->assertCount(1, $aProperties['cbSingle']['__after']);
        $this->assertCount(3, $aProperties['cbMultiple']['__after']);
        $this->assertCount(1, $aProperties['cbParams']['__after']);
        $this->assertArrayHasKey('funcAfter', $aProperties['cbSingle']['__after']);
        $this->assertArrayHasKey('funcAfter1', $aProperties['cbMultiple']['__after']);
        $this->assertArrayHasKey('funcAfter2', $aProperties['cbMultiple']['__after']);
        $this->assertArrayHasKey('funcAfter3', $aProperties['cbMultiple']['__after']);
        $this->assertArrayHasKey('funcAfter1', $aProperties['cbParams']['__after']);
        $this->assertIsArray($aProperties['cbSingle']['__after']['funcAfter']);
        $this->assertIsArray($aProperties['cbMultiple']['__after']['funcAfter1']);
        $this->assertIsArray($aProperties['cbMultiple']['__after']['funcAfter2']);
        $this->assertIsArray($aProperties['cbMultiple']['__after']['funcAfter3']);
        $this->assertIsArray($aProperties['cbParams']['__after']['funcAfter1']);
    }

    /**
     * @throws SetupException
     */
    public function testContainerAttribute()
    {
        $xMetadata = $this->getAttributes(Attribute::class, ['di1', 'di2']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(2, $aProperties);
        $this->assertArrayHasKey('di1', $aProperties);
        $this->assertArrayHasKey('di2', $aProperties);
        $this->assertCount(2, $aProperties['di1']['__di']);
        $this->assertCount(2, $aProperties['di2']['__di']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\ColorService', $aProperties['di1']['__di']['colorService']);
        $this->assertEquals('Jaxon\Attributes\Tests\Attr\Ajax\FontService', $aProperties['di1']['__di']['fontService']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\ColorService', $aProperties['di2']['__di']['colorService']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\TextService', $aProperties['di2']['__di']['textService']);
    }

    /**
     * @throws SetupException
     */
    public function testClassAttribute()
    {
        $xMetadata = $this->getAttributes(ClassAttribute::class, []);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(1, $aProperties);
        $this->assertArrayHasKey('*', $aProperties);
        $this->assertCount(5, $aProperties['*']);
        $this->assertArrayHasKey('bags', $aProperties['*']);
        $this->assertArrayHasKey('callback', $aProperties['*']);
        $this->assertArrayHasKey('__before', $aProperties['*']);
        $this->assertArrayHasKey('__after', $aProperties['*']);
        $this->assertArrayHasKey('__di', $aProperties['*']);

        $this->assertCount(2, $aProperties['*']['bags']);
        $this->assertEquals('user.name', $aProperties['*']['bags'][0]);
        $this->assertEquals('page.number', $aProperties['*']['bags'][1]);

        $this->assertCount(2, $aProperties['*']['__before']);
        $this->assertArrayHasKey('funcBefore1', $aProperties['*']['__before']);
        $this->assertArrayHasKey('funcBefore2', $aProperties['*']['__before']);
        $this->assertIsArray($aProperties['*']['__before']['funcBefore1']);
        $this->assertIsArray($aProperties['*']['__before']['funcBefore2']);

        $this->assertCount(3, $aProperties['*']['__after']);
        $this->assertArrayHasKey('funcAfter1', $aProperties['*']['__after']);
        $this->assertArrayHasKey('funcAfter2', $aProperties['*']['__after']);
        $this->assertArrayHasKey('funcAfter3', $aProperties['*']['__after']);
        $this->assertIsArray($aProperties['*']['__after']['funcAfter1']);
        $this->assertIsArray($aProperties['*']['__after']['funcAfter2']);
        $this->assertIsArray($aProperties['*']['__after']['funcAfter3']);

        $this->assertCount(3, $aProperties['*']['__di']);
        $this->assertArrayHasKey('colorService', $aProperties['*']['__di']);
        $this->assertArrayHasKey('textService', $aProperties['*']['__di']);
        $this->assertArrayHasKey('fontService', $aProperties['*']['__di']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\ColorService', $aProperties['*']['__di']['colorService']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\TextService', $aProperties['*']['__di']['textService']);
        $this->assertEquals('Jaxon\Attributes\Tests\Attr\Ajax\FontService', $aProperties['*']['__di']['fontService']);
    }

    /**
     * @throws SetupException
     */
    public function testClassExcludeAttribute()
    {
        $xMetadata = $this->getAttributes(ClassExcluded::class, ['doNot', 'withBags', 'cbSingle']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertTrue($bExcluded);
        $this->assertEmpty($aProperties);
        $this->assertEmpty($aProtected);
    }

    public function testExcludeAttributeError()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['doNotError']);
    }

    public function testDataBagAttributeError()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['withBagsError']);
    }

    public function testUploadAttributeWrongName()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['saveFilesWrongName']);
    }

    public function testUploadAttributeMultiple()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['saveFilesMultiple']);
    }

    public function testCallbacksBeforeAttributeNoCall()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['cbBeforeNoCall']);
    }

    public function testCallbacksBeforeAttributeUnknownAttr()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['cbBeforeUnknownAttr']);
    }

    public function testCallbacksBeforeAttributeWrongAttrType()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['cbBeforeWrongAttrType']);
    }

    public function testCallbacksAfterAttributeNoCall()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['cbAfterNoCall']);
    }

    public function testCallbacksAfterAttributeUnknownAttr()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['cbAfterUnknownAttr']);
    }

    public function testCallbacksAfterAttributeWrongAttrType()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['cbAfterWrongAttrType']);
    }

    public function testContainerAttributeUnknownAttr()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['diUnknownAttr']);
    }

    public function testContainerAttributeWrongAttrType()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['diWrongAttrType']);
    }

    public function testContainerAttributeWrongClassType()
    {
        $this->expectException(SetupException::class);
        $this->getAttributes(Attribute::class, ['diWrongClassType']);
    }
}
