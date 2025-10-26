<?php

/**
 * Export.php
 *
 * Jaxon attribute.
 * Specifies the methods to include in js export.
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

use function count;

#[Attribute(Attribute::TARGET_CLASS)]
class Export extends AbstractAttribute
{
    /**
     * @param array|null $base = null
     * @param array|null $only = null
     * @param array|null $except = null
     */
    public function __construct(private array|null $base = null,
        private array|null $only = null, private array|null $except = null)
    {}

    /**
     * @inheritDoc
     */
    public function saveValue(Metadata $xMetadata, string $sMethod = '*'): void
    {
        $aMethods = [];
        foreach(['base', 'only', 'except'] as $key)
        {
            if($this->$key !== null && count($this->$key) > 0)
            {
                $aMethods[$key] = $this->$key;
            }
        }

        $xMetadata->export($sMethod)->setMethods($aMethods);
    }
}
