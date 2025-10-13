<?php

/**
 * DataBag.php
 *
 * Jaxon attribute.
 * Specifies a data bag stored in the browser and included in ajax requests to a method.
 *
 * @package jaxon-attributes
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2024 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Attributes\Attribute;

use Jaxon\App\Metadata\Metadata;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Callback extends AbstractAttribute
{
    /**
     * @param string $name The javascript object name
     */
    public function __construct(private string $name)
    {}

    /**
     * @inheritDoc
     */
    public function saveValue(Metadata $xMetadata, string $sMethod = '*'): void
    {
        $xMetadata->callback($sMethod)->addValue($this->name);
    }
}
