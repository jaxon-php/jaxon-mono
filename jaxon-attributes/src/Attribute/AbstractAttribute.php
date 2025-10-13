<?php

/**
 * AbstractAttribute.php
 *
 * Base class for Jaxon class attributes.
 *
 * @package jaxon-attributes
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2024 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Attributes\Attribute;

use Jaxon\App\Metadata\Metadata;

abstract class AbstractAttribute
{
    /**
     * Save the annotation value
     *
     * @param Metadata $xMetadata
     * @param string $sMethod
     *
     * @return void
     */
    abstract public function saveValue(Metadata $xMetadata, string $sMethod = '*'): void;
}
