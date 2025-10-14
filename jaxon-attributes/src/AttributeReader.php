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
use Jaxon\App\Metadata\InputDataInterface;
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
        $nFilter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED;
        $aProperties = $xClass->getProperties($nFilter);
        foreach($aProperties as $xReflectionProperty)
        {
            $xType = $xReflectionProperty->getType();
            $sType = $xType?->getName() ?? '';
            // Check that the property has a valid type defined
            if($sType !== '' && is_a($xType, ReflectionNamedType::class))
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
            $xAttribute->setNamespace($xClass->getNamespaceName());
            $xAttribute->setTypes($this->aTypes[$xClass->getName()]);
        }
    }

    /**
     * @param ReflectionClass $xClass
     *
     * @return void
     */
    private function getClassExcludeAttr(ReflectionClass $xClass): void
    {
        $aClassAttributes = $xClass->getAttributes();
        $aAttributes = array_filter($aClassAttributes, fn($xAttribute) =>
            is_a($xAttribute->getName(), ExcludeAttribute::class, true));
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
    private function getClassAttrs(ReflectionClass $xClass): void
    {
        $aClassAttributes = $xClass->getAttributes();
        $aAttributes = array_filter($aClassAttributes, fn($xAttribute) =>
            is_a($xAttribute->getName(), AbstractAttribute::class, true) &&
            !is_a($xAttribute->getName(), ExcludeAttribute::class, true));
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
    private function getPropertyAttrs(ReflectionClass $xClass, string $sProperty): void
    {
        // Only Inject attributes are allowed on properties
        $aAttributes = !$xClass->hasProperty($sProperty) ? [] :
            $xClass->getProperty($sProperty)->getAttributes();
        $aAttributes = array_filter($aAttributes, fn($xAttribute) =>
            is_a($xAttribute->getName(), InjectAttribute::class, true));
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
    private function getMethodAttrs(ReflectionClass $xClass, string $sMethod): void
    {
        $aAttributes = !$xClass->hasMethod($sMethod) ? [] :
            $xClass->getMethod($sMethod)->getAttributes();
        $aAttributes = array_filter($aAttributes, fn($xAttribute) =>
            is_a($xAttribute->getName(), AbstractAttribute::class, true));
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
        return $xParentClass !== false &&
            in_array($xParentClass->getName(), $this->aJaxonClasses) ?
            $xParentClass : null;
    }

    /**
     * @inheritDoc
     * @throws SetupException
     */
    public function getAttributes(InputDataInterface $xInput): ?Metadata
    {
        try
        {
            $this->xMetadata = new Metadata();

            $xClass = $xInput->getReflectionClass();
            // First check if the class is exluded.
            $this->getClassExcludeAttr($xClass);
            if($this->xMetadata->isExcluded())
            {
                return $this->xMetadata;
            }

            $aClasses = [$xClass];
            while(($xClass = $this->getParentClass($xClass)) !== null)
            {
                $aClasses[] = $xClass;
            }
            $aClasses = array_reverse($aClasses);

            foreach($aClasses as $xClass)
            {
                // Processing class attributes
                $this->getClassAttrs($xClass);

                // Processing properties attributes
                foreach($xInput->getProperties() as $sProperty)
                {
                    $this->getPropertyAttrs($xClass, $sProperty);
                }

                // Processing methods attributes
                foreach($xInput->getMethods() as $sMethod)
                {
                    $this->getMethodAttrs($xClass, $sMethod);
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
