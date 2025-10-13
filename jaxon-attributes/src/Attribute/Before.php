<?php

/**
 * Before.php
 *
 * Jaxon attribute.
 * Specifies a method to be called before the one targeted by a Jaxon request.
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
class Before extends AbstractAttribute
{
    /**
     * @param string $call The method to call
     * @param array $with The call parameters
     */
    public function __construct(protected string $call, protected array $with = [])
    {}

    /**
     * @inheritDoc
     */
    public function saveValue(Metadata $xMetadata, string $sMethod = '*'): void
    {
        $xMetadata->before($sMethod)->addCall($this->call, $this->with);
    }
}
