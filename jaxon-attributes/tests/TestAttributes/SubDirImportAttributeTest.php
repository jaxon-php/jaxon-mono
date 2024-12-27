<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\TestAttributes;

use Jaxon\App\Metadata\MetadataReaderInterface;
use Jaxon\Attributes\Tests\Attr\Ajax\SubDirImportAttribute;
use Jaxon\Exception\SetupException;
use PHPUnit\Framework\TestCase;

use function Jaxon\jaxon;

class SubDirImportAttributeTest extends TestCase
{
    /**
     * @var string
     */
    private $sCacheDir;

    /**
     * @var MetadataReaderInterface
     */
    protected $xMetadataReader;

    /**
     * @throws SetupException
     */
    public function setUp(): void
    {
        $this->sCacheDir = __DIR__ . '/../cache';
        @mkdir($this->sCacheDir);

        jaxon()->di()->val('jaxon_attributes_cache_dir', $this->sCacheDir);
        $this->xMetadataReader = jaxon()->di()->getMetadataReader('attributes');
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
        [$bExcluded, $aProperties, ] = $this->xMetadataReader->getAttributes(SubDirImportAttribute::class, ['attrDi'], ['secondService']);

        $this->assertFalse($bExcluded);

        $this->assertCount(2, $aProperties);
        $this->assertArrayHasKey('attrDi', $aProperties);
        $this->assertCount(1, $aProperties['attrDi']['__di']);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\SubDir\FirstService', $aProperties['attrDi']['__di']['firstService']);
        $this->assertArrayHasKey('*', $aProperties);
        $this->assertEquals('Jaxon\Attributes\Tests\Service\SubDir\SecondService', $aProperties['*']['__di']['secondService']);
    }
}
