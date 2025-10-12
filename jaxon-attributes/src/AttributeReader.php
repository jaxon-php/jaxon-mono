<?php

/**
 * AttributeReader.php
 *
 * Jaxon attribute reader.
 *
 * @package jaxon-attributes
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2024 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Attributes;

use Jaxon\App\Metadata\InputDataInterface;
use Jaxon\App\Metadata\Metadata;
use Jaxon\App\Metadata\MetadataReaderInterface;
use Jaxon\Attributes\Attribute\AbstractAttribute;
use Jaxon\Attributes\Attribute\DI as DiAttribute;
use Jaxon\Exception\SetupException;
use Error;
use Exception;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

use function array_filter;
use function array_merge;
use function array_reverse;
use function count;
use function is_a;

class AttributeReader implements MetadataReaderInterface
{
    /**
     * @var ReflectionClass
     */
    protected $xReflectionClass;

    /**
     * Imports defined with "use" statements in file headers.
     *
     * @var array
     */
    protected $aImportedTypes;

    /**
     * Properties types.
     *
     * @var array
     */
    protected $aPropertyTypes;

    /**
     * @param AttributeParser $xParser
     * @param string $sCacheDir
     */
    public function __construct(private AttributeParser $xParser, private string $sCacheDir)
    {}

    /**
     * Read the property types
     *
     * @param ReflectionClass $xReflectionClass
     *
     * @return void
     */
    private function readImportedTypes(ReflectionClass $xReflectionClass)
    {
        $sClass = $xReflectionClass->getName();
        if(isset($this->aImportedTypes[$sClass]))
        {
            return;
        }

        $this->aImportedTypes[$sClass] = $this->xParser->readImportedTypes($xReflectionClass);
    }

    /**
     * Read the property types
     *
     * @param ReflectionClass $xReflectionClass
     *
     * @return void
     */
    private function readPropertyTypes(ReflectionClass $xReflectionClass): void
    {
        $sClass = $xReflectionClass->getName();
        if(isset($this->aPropertyTypes[$sClass]))
        {
            return;
        }

        $this->aPropertyTypes[$sClass] = [];
        $nFilter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED;
        $aProperties = $xReflectionClass->getProperties($nFilter);
        foreach($aProperties as $xReflectionProperty)
        {
            $xType = $xReflectionProperty->getType();
            // Check that the property has a valid type defined
            if(is_a($xType, ReflectionNamedType::class) && $xType->getName() !== '')
            {
                $this->aPropertyTypes[$sClass][$xReflectionProperty->getName()] = $xType->getName();
            }
        }
    }

    /**
     * @param ReflectionClass $xReflectionClass
     * @param ReflectionAttribute $xAttribute
     * @param array $aValues The current values of the attribute
     * @param string $sProperty
     *
     * @return array
     * @throws SetupException
     */
    private function getAttrValue(ReflectionClass $xReflectionClass,
        ReflectionAttribute $xAttribute, array $aValues, string $sProperty = ''): array
    {
        $xInstance = $xAttribute->newInstance();
        $xInstance->setTarget($xAttribute->getTarget());
        $xInstance->setNamespace($xReflectionClass->getNamespaceName());
        $sName = $xInstance->getName();
        if(is_a($xInstance, DiAttribute::class))
        {
            $sClass = $xReflectionClass->getName();
            $xInstance->setTypes($this->aImportedTypes[$sClass], $this->aPropertyTypes[$sClass]);
            if($sProperty !== '')
            {
                $xInstance->setProperty($sProperty);
            }
        }

        $xInstance->validateArguments($xAttribute->getArguments());
        $xInstance->setPrevValue($aValues[$sName] ?? null);

        return [$sName, $xInstance->getValidatedValue()];
    }

    /**
     * @param ReflectionClass $xReflectionClass
     * @param array<ReflectionAttribute> $aAttributes
     *
     * @return array
     * @throws SetupException
     */
    private function getAttrValues(ReflectionClass $xReflectionClass, array $aAttributes): array
    {
        // Only keep our attributes.
        $aAttributes = array_filter($aAttributes, function($xAttribute) {
            return is_a($xAttribute->getName(), AbstractAttribute::class, true);
        });

        $aValues = [];
        foreach($aAttributes as $xAttribute)
        {
            [$sName, $xValue] = $this->getAttrValue($xReflectionClass, $xAttribute, $aValues);
            if($sName !== 'protected' || ($xValue)) // Ignore attribute Exclude with value true
            {
                $aValues[$sName] = $xValue;
            }
        }
        return $aValues;
    }

    /**
     * @param ReflectionClass $xReflectionClass
     * @param string $sProperty
     *
     * @return array
     * @throws SetupException
     */
    private function getPropertyAttrValues(ReflectionClass $xReflectionClass, string $sProperty): array
    {
        // Only keep our attributes.
        $aAttributes = $xReflectionClass->getProperty($sProperty)->getAttributes();
        $aAttributes = array_filter($aAttributes, function($xAttribute) {
            // Only DI attributes are allowed on properties
            return is_a($xAttribute->getName(), DiAttribute::class, true);
        });

        $nCount = count($aAttributes);
        if($nCount === 0)
        {
            return ['', null];
        }
        if($nCount > 1)
        {
            throw new SetupException('Only one DI attribute is allowed on a property');
        }

        return $this->getAttrValue($xReflectionClass, $aAttributes[0], [], $sProperty);
    }

    /**
     * @param array $aAttributes
     *
     * @return array
     */
    private function mergeAttributes(array $aAttributes): array
    {
        return array_merge(...$aAttributes);
    }

    /**
     * @param array<ReflectionClass> $aReflectionClasses
     *
     * @return array
     */
    private function getClassAttributes(array $aReflectionClasses): array
    {
        $fCb = fn(ReflectionClass $xClass) =>
            $this->getAttrValues($xClass, $xClass->getAttributes());
        return $this->mergeAttributes(array_map($fCb, $aReflectionClasses));
    }

    /**
     * @param array<ReflectionClass> $aReflectionClasses
     * @param array<string> $aProperties
     *
     * @return array
     */
    private function getPropsAttributes(array $aReflectionClasses, array $aProperties): array
    {
        $aPropAttrs = [];
        foreach($aReflectionClasses as $xReflectionClass)
        {
            // Properties attributes
            foreach($aProperties as $sProperty)
            {
                [$sName, $xValue] = $this->getPropertyAttrValues($xReflectionClass, $sProperty);
                if($xValue !== null)
                {
                    $aPropAttrs[$sName] = array_merge($aPropAttrs[$sName] ?? [], $xValue);
                }
            }
        }
        return $aPropAttrs;
    }

    /**
     * @param array<ReflectionClass> $aReflectionClasses
     * @param array<string> $aMethods
     *
     * @return array
     */
    private function getMethodsAttributes(array $aReflectionClasses, array $aMethods): array
    {
        $aMethodsValues = [];
        foreach($aReflectionClasses as $xReflectionClass)
        {
            foreach($aMethods as $sMethod)
            {
                $aAttributes = $xReflectionClass->getMethod($sMethod)?->getAttributes() ?? [];
                $aMethodsValues[$sMethod] = $this->getAttrValues($xReflectionClass, $aAttributes);
            }
        }
        return $aMethodsValues;
    }

    /**
     * @inheritDoc
     * @throws SetupException
     */
    public function getAttributes(InputDataInterface $xInput): ?Metadata
    {
        $xReflectionClass = $xInput->getReflectionClass();
        $aReflectionClasses = [];
        do
        {
            $aReflectionClasses[] = $xReflectionClass;
            $this->readImportedTypes($xReflectionClass);
            $this->readPropertyTypes($xReflectionClass);
            $xReflectionClass = $xReflectionClass->getParentClass();
        }
        while($xReflectionClass);
        $aReflectionClasses = array_reverse($aReflectionClasses);

        try
        {
            // Processing class attributes
            $aClassAttrs = $this->getClassAttributes($aReflectionClasses);
            if(isset($aClassAttrs['protected']))
            {
                // The entire class is not to be exported.
                return new Metadata(true, [], []);
            }

            // Processing properties attributes
            $aPropsAttrs = $this->getPropsAttributes($aReflectionClasses, $xInput->getProperties());

            // Merge attributes and class attributes
            foreach($aPropsAttrs as $sName => $xValue)
            {
                $aClassAttrs[$sName] = array_merge($aClassAttrs[$sName] ?? [], $xValue);
            }

            // Processing methods attributes
            $aMethodsAttrs = $this->getMethodsAttributes($aReflectionClasses, $xInput->getMethods());
            $aAttrValues = array_filter($aMethodsAttrs, fn($aMethodAttrs) =>
                !isset($aMethodAttrs['protected']) && count($aMethodAttrs) > 0);
            if(count($aClassAttrs) > 0)
            {
                $aAttrValues['*'] = $aClassAttrs;
            }
            $aProtected = array_filter(array_keys($aMethodsAttrs),
                fn($sMethod) => isset($aMethodsAttrs[$sMethod]['protected']));

            return new Metadata(false, $aAttrValues, $aProtected);
        }
        catch(Exception|Error $e)
        {
            throw new SetupException($e->getMessage());
        }
    }
}
