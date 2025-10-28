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

use Jaxon\App;
use Jaxon\App\Metadata\InputData;
use Jaxon\App\Metadata\Metadata;
use Jaxon\App\Metadata\MetadataReaderInterface;
use Jaxon\Attributes\Attribute\AbstractAttribute;
use Jaxon\Attributes\Attribute\Inject as InjectAttribute;
use Jaxon\Attributes\Attribute\Exclude as ExcludeAttribute;
use Jaxon\Exception\SetupException;
use Error;
use Exception;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

use function array_filter;
use function array_reverse;
use function count;
use function in_array;
use function is_a;

class AttributeReader implements MetadataReaderInterface
{
    /**
     * @var Metadata
     */
    protected Metadata $xMetadata;

    /**
     * @var array
     */
    protected array $aTypes;

    /**
     * @var array
     */
    private $aJaxonClasses = [
        App\Component::class,
        App\NodeComponent::class,
        App\FuncComponent::class,
        App\PageComponent::class,
        App\CallableClass::class,
    ];

    /**
     * @var array
     */
    private $aBaseAttributes = [
        ExcludeAttribute::class,
    ];

    /**
     * @var array
     */
    private $aPropertyAttributes = [
        InjectAttribute::class,
    ];

    /**
     * Read the property types
     *
     * @param ReflectionClass $xClass
     *
     * @return void
     */
    private function readTypes(ReflectionClass $xClass)
    {
        $sClass = $xClass->getName();
        if(isset($this->aTypes[$sClass]))
        {
            return;
        }

        $this->aTypes[$sClass] = [];
        $aProperties = $xClass->getProperties(ReflectionProperty::IS_PUBLIC |
            ReflectionProperty::IS_PROTECTED);
        foreach($aProperties as $xReflectionProperty)
        {
            $xType = $xReflectionProperty->getType();
            // Check that the property has a valid type defined
            if(is_a($xType, ReflectionNamedType::class) &&
                ($sType = $xType->getName()) !== '')
            {
                $this->aTypes[$sClass][$xReflectionProperty->getName()] = $sType;
            }
        }
    }

    /**
     * @param AbstractAttribute $xAttribute
     * @param ReflectionClass $xClass
     * @param ReflectionAttribute $xReflectionAttribute
     *
     * @return void
     */
    private function initAttribute(AbstractAttribute $xAttribute,
        ReflectionClass $xClass, ReflectionAttribute $xReflectionAttribute): void
    {
        if(is_a($xAttribute, InjectAttribute::class))
        {
            $this->readTypes($xClass);
            $xAttribute->setTarget($xReflectionAttribute->getTarget());
            $xAttribute->setTypes($this->aTypes[$xClass->getName()]);
        }
    }

    /**
     * @param ReflectionAttribute $xReflectionAttribute
     *
     * @return bool
     */
    private function isJaxonAttribute(ReflectionAttribute $xReflectionAttribute): bool
    {
        return is_a($xReflectionAttribute->getName(), AbstractAttribute::class, true);
    }

    /**
     * @param ReflectionAttribute $xReflectionAttribute
     *
     * @return bool
     */
    private function isBaseClassAttribute(ReflectionAttribute $xReflectionAttribute): bool
    {
        return in_array($xReflectionAttribute->getName(), $this->aBaseAttributes);
    }

    /**
     * @param ReflectionAttribute $xReflectionAttribute
     *
     * @return bool
     */
    private function isPropertyAttribute(ReflectionAttribute $xReflectionAttribute): bool
    {
        return in_array($xReflectionAttribute->getName(), $this->aPropertyAttributes);
    }

    /**
     * @param ReflectionClass $xClass
     *
     * @return void
     */
    private function readBaseClassAttributes(ReflectionClass $xClass): void
    {
        $aAttributes = $xClass->getAttributes();
        $aAttributes = array_filter($aAttributes, fn($xReflectionAttribute) =>
            $this->isBaseClassAttribute($xReflectionAttribute));
        foreach($aAttributes as $xReflectionAttribute)
        {
            $xReflectionAttribute->newInstance()->saveValue($this->xMetadata);
        }
    }

    /**
     * @param ReflectionClass $xClass
     *
     * @return void
     */
    private function readClassAttributes(ReflectionClass $xClass): void
    {
        $aAttributes = $xClass->getAttributes();
        $aAttributes = array_filter($aAttributes, fn($xReflectionAttribute) =>
            $this->isJaxonAttribute($xReflectionAttribute) &&
            !$this->isBaseClassAttribute($xReflectionAttribute));
        foreach($aAttributes as $xReflectionAttribute)
        {
            $xAttribute = $xReflectionAttribute->newInstance();
            $this->initAttribute($xAttribute, $xClass, $xReflectionAttribute);

            $xAttribute->saveValue($this->xMetadata);
        }
    }

    /**
     * @param ReflectionClass $xClass
     * @param string $sProperty
     *
     * @return void
     * @throws SetupException
     */
    private function readPropertyAttributes(ReflectionClass $xClass, string $sProperty): void
    {
        $aAttributes = !$xClass->hasProperty($sProperty) ? [] :
            $xClass->getProperty($sProperty)->getAttributes();
        $aAttributes = array_filter($aAttributes, fn($xReflectionAttribute) =>
            $this->isPropertyAttribute($xReflectionAttribute));
        // Only Inject attributes are allowed on properties
        if(count($aAttributes) > 1)
        {
            throw new SetupException('Only one Inject attribute is allowed on a property');
        }

        foreach($aAttributes as $xReflectionAttribute)
        {
            /** @var InjectAttribute */
            $xAttribute = $xReflectionAttribute->newInstance();
            $xAttribute->setAttr($sProperty);
            $this->initAttribute($xAttribute, $xClass, $xReflectionAttribute);

            $xAttribute->saveValue($this->xMetadata);
        }
    }

    /**
     * @param ReflectionClass $xClass
     * @param string $sMethod
     *
     * @return void
     */
    private function readMethodAttributes(ReflectionClass $xClass, string $sMethod): void
    {
        $aAttributes = !$xClass->hasMethod($sMethod) ? [] :
            $xClass->getMethod($sMethod)->getAttributes();
        $aAttributes = array_filter($aAttributes, fn($xReflectionAttribute) =>
            $this->isJaxonAttribute($xReflectionAttribute));
        foreach($aAttributes as $xReflectionAttribute)
        {
            $xAttribute = $xReflectionAttribute->newInstance();
            $this->initAttribute($xAttribute, $xClass, $xReflectionAttribute);

            $xAttribute->saveValue($this->xMetadata, $sMethod);
        }
    }

    /**
     * @param ReflectionClass $xClass
     *
     * @return ReflectionClass|null
     */
    private function getParentClass(ReflectionClass $xClass): ReflectionClass|null
    {
        $xParentClass = $xClass->getParentClass();
        return $xParentClass === false || in_array($xParentClass->getName(),
            $this->aJaxonClasses) ? null : $xParentClass;
    }

    /**
     * @inheritDoc
     * @throws SetupException
     */
    public function getAttributes(InputData $xInput): Metadata
    {
        try
        {
            $this->xMetadata = new Metadata();

            $xClass = $xInput->getReflectionClass();
            $this->readBaseClassAttributes($xClass);

            $aClasses = [$xClass];
            while(($xClass = $this->getParentClass($xClass)) !== null)
            {
                $aClasses[] = $xClass;
            }
            $aClasses = array_reverse($aClasses);

            foreach($aClasses as $xClass)
            {
                // Processing class attributes
                $this->readClassAttributes($xClass);
                // Processing properties attributes
                foreach($xInput->getProperties() as $sProperty)
                {
                    $this->readPropertyAttributes($xClass, $sProperty);
                }
            }

            // The methods attributes are not taken for excluded classes.
            if($this->xMetadata->isExcluded())
            {
                return $this->xMetadata;
            }

            foreach($aClasses as $xClass)
            {
                // Processing methods attributes
                foreach($xInput->getMethods() as $sMethod)
                {
                    $this->readMethodAttributes($xClass, $sMethod);
                }
            }

            return $this->xMetadata;
        }
        catch(Exception|Error $e)
        {
            throw new SetupException($e->getMessage());
        }
    }
}
