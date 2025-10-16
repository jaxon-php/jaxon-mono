<?php

namespace Jaxon\Attributes\Tests\TestAttributes;

use Jaxon\Attributes\Tests\AttributeTrait;
use Jaxon\Attributes\Tests\Attr\Ajax\ClassExtendsExcluded;
use Jaxon\Attributes\Tests\Attr\Ajax\ClassExtendsTraitExcluded;
use Jaxon\Exception\SetupException;
use PHPUnit\Framework\TestCase;

use function Jaxon\Attributes\_register;
use function Jaxon\jaxon;

class ClassExtendsTest extends TestCase
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
    public function testInheritedAttributes()
    {
        $xMetadata = $this->getAttributes(ClassExtendsExcluded::class,
            ['doNot', 'withBags', 'cbSingle']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(1, $aProtected);
        $this->assertEquals('doNot', $aProtected[0]);

        $this->assertCount(2, $aProperties);

        $this->assertArrayHasKey('withBags', $aProperties);
        $this->assertCount(1, $aProperties['withBags']);
        $this->assertCount(2, $aProperties['withBags']['bags']);
        $this->assertEquals('user.name', $aProperties['withBags']['bags'][0]);
        $this->assertEquals('page.number', $aProperties['withBags']['bags'][1]);

        $this->assertArrayHasKey('cbSingle', $aProperties);
        $this->assertCount(1, $aProperties['cbSingle']['__before']);
        $this->assertArrayHasKey('funcBefore', $aProperties['cbSingle']['__before']);
        $this->assertIsArray($aProperties['cbSingle']['__before']['funcBefore']);
        $this->assertCount(1, $aProperties['cbSingle']['__after']);
        $this->assertArrayHasKey('funcAfter', $aProperties['cbSingle']['__after']);
        $this->assertIsArray($aProperties['cbSingle']['__after']['funcAfter']);
    }

    /**
     * @throws SetupException
     */
    public function testInheritedTraitAttributes()
    {
        $xMetadata = $this->getAttributes(ClassExtendsTraitExcluded::class,
            ['doNot', 'withBags', 'cbSingle', 'cbMultiple', 'withCallbacks']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);
        $this->assertEmpty($aProtected);

        $this->assertCount(4, $aProperties);

        $this->assertArrayHasKey('withBags', $aProperties);
        $this->assertCount(1, $aProperties['withBags']);
        $this->assertCount(2, $aProperties['withBags']['bags']);
        $this->assertEquals('user.name', $aProperties['withBags']['bags'][0]);
        $this->assertEquals('page.number', $aProperties['withBags']['bags'][1]);

        $this->assertArrayHasKey('cbSingle', $aProperties);
        $this->assertCount(1, $aProperties['cbSingle']['__before']);
        $this->assertArrayHasKey('funcBefore', $aProperties['cbSingle']['__before']);
        $this->assertIsArray($aProperties['cbSingle']['__before']['funcBefore']);
        $this->assertCount(1, $aProperties['cbSingle']['__after']);
        $this->assertArrayHasKey('funcAfter', $aProperties['cbSingle']['__after']);
        $this->assertIsArray($aProperties['cbSingle']['__after']['funcAfter']);

        $this->assertArrayHasKey('cbMultiple', $aProperties);
        $this->assertCount(2, $aProperties['cbMultiple']['__before']);
        $this->assertArrayHasKey('funcBefore1', $aProperties['cbMultiple']['__before']);
        $this->assertArrayHasKey('funcBefore2', $aProperties['cbMultiple']['__before']);
        $this->assertIsArray($aProperties['cbMultiple']['__before']['funcBefore1']);
        $this->assertIsArray($aProperties['cbMultiple']['__before']['funcBefore2']);
        $this->assertCount(3, $aProperties['cbMultiple']['__after']);
        $this->assertArrayHasKey('funcAfter1', $aProperties['cbMultiple']['__after']);
        $this->assertArrayHasKey('funcAfter2', $aProperties['cbMultiple']['__after']);
        $this->assertArrayHasKey('funcAfter3', $aProperties['cbMultiple']['__after']);
        $this->assertIsArray($aProperties['cbMultiple']['__after']['funcAfter1']);
        $this->assertIsArray($aProperties['cbMultiple']['__after']['funcAfter2']);
        $this->assertIsArray($aProperties['cbMultiple']['__after']['funcAfter3']);

        $this->assertArrayHasKey('withCallbacks', $aProperties);
        $this->assertCount(1, $aProperties['withCallbacks']);
        $this->assertCount(2, $aProperties['withCallbacks']['callback']);
        $this->assertEquals('jaxon.callback.first', $aProperties['withCallbacks']['callback'][0]);
        $this->assertEquals('jaxon.callback.second', $aProperties['withCallbacks']['callback'][1]);
    }
}
