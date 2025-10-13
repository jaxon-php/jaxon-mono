<?php

/**
 * Exclude.php
 *
 * Jaxon attribute.
 * Specifies if a class or method is excluded from js export.
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

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Exclude extends AbstractAttribute
{
    /**
     * @param bool $value
     */
    public function __construct(private bool $value = true)
    {}

    /**
     * @inheritDoc
     */
    public function saveValue(Metadata $xMetadata, string $sMethod = '*'): void
    {
        $xMetadata->exclude($sMethod)->setValue($this->value);
    }
}
