<?php

/**
 * Upload.php
 *
 * Jaxon attribute.
 * Specifies an upload form field id.
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

#[Attribute(Attribute::TARGET_METHOD)]
class Upload extends AbstractAttribute
{
    /**
     * @param string $field The name of the upload field
     */
    public function __construct(private string $field)
    {}

    /**
     * @inheritDoc
     */
    public function saveValue(Metadata $xMetadata, string $sMethod = '*'): void
    {
        $xMetadata->upload($sMethod)->setValue($this->field);
    }
}
