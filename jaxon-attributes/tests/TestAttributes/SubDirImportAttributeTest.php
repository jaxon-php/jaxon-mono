<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\TestAttributes;

use Jaxon\Attributes\Tests\AttributeTrait;
use Jaxon\Attributes\Tests\Attr\Ajax\SubDirImportAttribute;
use Jaxon\Exception\SetupException;
use PHPUnit\Framework\TestCase;

use function Jaxon\Attributes\_register;
use function Jaxon\jaxon;

class SubDirImportAttributeTest extends TestCase
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

    public function testCbBeforeAttributeErrorNumber()
    {
        $xMetadata = $this->getAttributes(SubDirImportAttribute::class, ['attrDi'], ['secondService']);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();
        $aProtected = $xMetadata->getProtectedMethods();

        $this->assertFalse($bExcluded);

        $this->assertCount(2, $aProperties);
        $this->assertArrayHasKey('attrDi', $aProperties);
        $this->assertCount(1, $aProperties['attrDi']['__di']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\SubDir\FirstService', $aProperties['attrDi']['__di']['firstService']);
        $this->assertArrayHasKey('*', $aProperties);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\SubDir\SecondService', $aProperties['*']['__di']['secondService']);
    }
}
