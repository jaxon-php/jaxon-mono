<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\TestAttributes;

use Jaxon\Attributes\Tests\AttributeTrait;
use Jaxon\Attributes\Tests\Attr\Ajax\SubDirImportAttribute;
use Jaxon\Attributes\Tests\Service\SubDir\FirstService;
use Jaxon\Attributes\Tests\Service\SubDir\SecondService;
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
        $xMetadata = $this->getAttributes(SubDirImportAttribute::class, ['attrDi'],
            [SubDirImportAttribute::class => ['secondService']]);
        $bExcluded = $xMetadata->isExcluded();
        $aProperties = $xMetadata->getProperties();

        $this->assertFalse($bExcluded);

        $di1 = $aProperties['attrDi']['__di'];
        $di2 = $aProperties['*']['__di'];
        $this->assertCount(2, $aProperties);
        $this->assertArrayHasKey('attrDi', $aProperties);
        $this->assertCount(1, $di1);
        $this->assertEquals(FirstService::class, $di1['firstService'][0]);
        $this->assertEquals(SubDirImportAttribute::class, $di1['firstService'][1]);
        $this->assertArrayHasKey('*', $aProperties);
        $this->assertEquals(SecondService::class, $di2['secondService'][0]);
        $this->assertEquals(SubDirImportAttribute::class, $di2['secondService'][1]);
    }
}
