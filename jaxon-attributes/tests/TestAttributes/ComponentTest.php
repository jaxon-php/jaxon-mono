<?php

namespace Jaxon\Attributes\Tests\TestAttributes;

use Jaxon\Attributes\Tests\AttributeTrait;
use Jaxon\Attributes\Tests\Attr\Ajax\Component\FuncComponent;
use Jaxon\Attributes\Tests\Attr\Ajax\Component\NodeComponent;
use Jaxon\Attributes\Tests\Attr\Ajax\Component\PageComponent;
use Jaxon\Exception\SetupException;
use PHPUnit\Framework\TestCase;

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
            [], ['item', 'html', 'render', 'clear', 'visible']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);
        $this->assertCount(0, $aProperties);
        $this->assertCount(0, $aProtected);
    }

    /**
     * @throws SetupException
     */
    public function testPageComponentExportMethods()
    {
        $xMetadata = $this->getAttributes(PageComponent::class,
            [], ['item', 'html', 'render', 'clear', 'visible']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);
        $this->assertCount(0, $aProperties);
        $this->assertCount(0, $aProtected);
    }

    /**
     * @throws SetupException
     */
    public function testFuncComponentExportMethods()
    {
        $xMetadata = $this->getAttributes(FuncComponent::class,
            [], ['paginator']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);
        $this->assertCount(0, $aProperties);
        $this->assertCount(0, $aProtected);
    }

    // public function testContainerAttributeErrorTwoDi()
    // {
    //     $this->expectException(SetupException::class);
    //     $this->getAttributes(PropertyAttribute::class, [], ['errorTwoDi']);
    // }

    // public function testContainerAttributeErrorDiClass()
    // {
    //     $this->expectException(SetupException::class);
    //     $this->getAttributes(PropertyAttribute::class, ['errorDiClass']);
    // }
}
