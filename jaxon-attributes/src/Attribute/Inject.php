<?php

/**
 * Inject.php
 *
 * Jaxon attribute.
 * Specifies attributes to inject into a callable object.
 *
 * @package jaxon-attributes
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2024 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Attributes\Attribute;

use Jaxon\App\Metadata\Metadata;
use Jaxon\Exception\SetupException;
use Attribute;

use function ltrim;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Inject extends AbstractAttribute
{
    /**
     * @var int
     */
    protected $nTarget;

    /**
     * @var array
     */
    protected array $aTypes;

    /**
     * @param string|null $type
     * @param string|null $attr
     */
    public function __construct(protected string|null $type = null,
        protected string|null $attr = null)
    {}

    /**
     * @param int $nTarget
     *
     * @return void
     */
    public function setTarget(int $nTarget): void
    {
        $this->nTarget = $nTarget;
    }

    /**
     * @param string $sAttr
     *
     * @return void
     */
    public function setAttr(string $sAttr): void
    {
        $this->attr = $sAttr;
    }

    /**
     * @param array $aTypes
     *
     * @return void
     */
    public function setTypes(array $aTypes): void
    {
        $this->aTypes = $aTypes;
    }

    /**
     * @return void
     */
    public function validate(): void
    {
        if($this->nTarget === Attribute::TARGET_CLASS)
        {
            if(!$this->attr || !$this->type)
            {
                throw new SetupException('When applied to a class, the Inject attribute requires two arguments.');
            }
            return;
        }
        if($this->nTarget === Attribute::TARGET_METHOD)
        {
            if(!$this->attr)
            {
                throw new SetupException('When applied to a method, the Inject attribute requires the "attr" argument.');
            }
            return;
        }
    }

    /**
     * @return void
     */
    private function getFullClassName(): void
    {
        if(!$this->type)
        {
            // If no type is provided, take the attribute type.
            $this->type = $this->aTypes[$this->attr] ?? '';
            return;
        }
        if($this->type[0] === '\\')
        {
            $this->type = ltrim($this->type, '\\');
        }
    }

    /**
     * @inheritDoc
     */
    public function saveValue(Metadata $xMetadata, string $sMethod = '*'): void
    {
        $this->validate();
        $this->getFullClassName();
        $xMetadata->container($sMethod)->addValue($this->attr, $this->type);
    }
}
