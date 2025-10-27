<?php

namespace Jaxon\Attributes\Tests;

use Jaxon\App\Metadata\InputData;
use Jaxon\App\Metadata\Metadata;
use Jaxon\Config\ConfigSetter;
use Jaxon\Plugin\Request\CallableClass\ComponentOptions;
use ReflectionClass;

use function Jaxon\jaxon;

trait AttributeTrait
{
    /**
     * Get the metadata from a given class
     *
     * @param ReflectionClass|string $xClass
     * @param array $aMethods
     * @param array $aProperties
     *
     * @return Metadata|null
     */
    public function getAttributes(ReflectionClass|string $xClass,
        array $aMethods = [], array $aProperties = []): ?Metadata
    {
        $xInputData = new InputData($xClass, $aMethods, $aProperties);
        $xMetadataReader = jaxon()->di()->getMetadataReader('attributes');
        return $xMetadataReader->getAttributes($xInputData);
    }

    /**
     * @param ReflectionClass $xClass
     *
     * @return ComponentOptions
     */
    public function getOptions(ReflectionClass $xClass): ComponentOptions
    {
        $xConfigSetter = new ConfigSetter();
        $aOptions = [
            'separator' => '.',
            'protected' => [],
            'functions' => [],
            'config' => $xConfigSetter->newConfig(['metadata' => ['format' => 'attributes']]),
        ];
        return jaxon()->cdi()->getComponentOptions($xClass, $aOptions);
    }
}
