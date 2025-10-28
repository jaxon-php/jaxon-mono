<?php

namespace Jaxon\Attributes\Tests\TestAttributes;

use Jaxon\Attributes\Tests\AttributeTrait;
use Jaxon\Attributes\Tests\Attr\Ajax\Component\FuncComponent;
use Jaxon\Attributes\Tests\Attr\Ajax\Component\ExportErrorComponent;
use Jaxon\Attributes\Tests\Attr\Ajax\Component\NodeComponent;
use Jaxon\Attributes\Tests\Attr\Ajax\Component\NodeBaseComponent;
use Jaxon\Attributes\Tests\Attr\Ajax\Component\PageComponent;
use Jaxon\Exception\SetupException;
use PHPUnit\Framework\TestCase;

use ReflectionClass;
use function Jaxon\Attributes\_register;
use function Jaxon\jaxon;

class ComponentTest extends TestCase
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
    public function testNodeComponentExportMethods()
    {
        $xMetadata = $this->getAttributes(NodeComponent::class,
            ['item', 'html', 'render', 'clear', 'visible'], []);
        $bExcluded = $xMetadata->isExcluded();
        $aExcluded = $xMetadata->getExceptMethods();
        $aBaseMethods = $xMetadata->getExportBaseMethods();
        $aOnlyMethods = $xMetadata->getExportOnlyMethods();

        $this->assertFalse($bExcluded);
        $this->assertCount(0, $aExcluded);
        $this->assertCount(0, $aBaseMethods);
        $this->assertCount(0, $aOnlyMethods);
    }

    /**
     * @throws SetupException
     */
    public function testPageComponentExportMethods()
    {
        $xMetadata = $this->getAttributes(PageComponent::class,
            ['item', 'html', 'render', 'clear', 'visible'], []);
        $bExcluded = $xMetadata->isExcluded();
        $aExcluded = $xMetadata->getExceptMethods();
        $aBaseMethods = $xMetadata->getExportBaseMethods();
        $aOnlyMethods = $xMetadata->getExportOnlyMethods();

        $this->assertFalse($bExcluded);
        $this->assertCount(0, $aExcluded);
        $this->assertCount(0, $aBaseMethods);
        $this->assertCount(0, $aOnlyMethods);
    }

    /**
     * @throws SetupException
     */
    public function testFuncComponentExportMethods()
    {
        $xMetadata = $this->getAttributes(FuncComponent::class,
            ['paginator'], []);
        $bExcluded = $xMetadata->isExcluded();
        $aExcluded = $xMetadata->getExceptMethods();
        $aBaseMethods = $xMetadata->getExportBaseMethods();
        $aOnlyMethods = $xMetadata->getExportOnlyMethods();

        $this->assertFalse($bExcluded);
        $this->assertCount(0, $aExcluded);
        $this->assertCount(0, $aBaseMethods);
        $this->assertCount(0, $aOnlyMethods);
    }

    /**
     * @throws SetupException
     */
    public function testNodeComponentExportBaseMethods()
    {
        // The attribute exports the 'html' and 'render' methods,
        // but only the 'render' method shall be exported.
        $xClass = new ReflectionClass(NodeBaseComponent::class);
        $aMethods = ['item', 'html', 'render', 'clear', 'visible'];
        $xMetadata = $this->getAttributes($xClass, $aMethods, []);
        $aBaseMethods = $xMetadata->getExportBaseMethods();

        // The 'html' and 'render' methods are returned.
        $this->assertCount(2, $aBaseMethods);

        $xOptions = $this->getOptions($xClass);
        $aPublicMethods = $xOptions->getPublicMethods();

        // Only the 'render' method is returned.
        $this->assertCount(1, $aPublicMethods);
        $this->assertEquals('render', $aPublicMethods[0]);
    }

    public function testAttributeExportError()
    {
        $this->expectException(SetupException::class);
        $xClass = new ReflectionClass(ExportErrorComponent::class);
        $this->getOptions($xClass);
    }

    // public function testContainerAttributeErrorDiClass()
    // {
    //     $this->expectException(SetupException::class);
    //     $this->getAttributes(PropertyAttribute::class, ['errorDiClass']);
    // }
}
