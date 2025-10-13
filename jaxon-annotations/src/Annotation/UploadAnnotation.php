<?php

/**
 * UploadAnnotation.php
 *
 * Jaxon annotation for file upload.
 *
 * @package jaxon-annotations
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2022 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-annotations
 */

namespace Jaxon\Annotations\Annotation;

use Jaxon\App\Metadata\Metadata;
use mindplay\annotations\AnnotationException;

use function count;
use function is_string;
use function preg_split;

/**
 * Specifies an upload form field id.
 *
 * @usage('method' => true)
 */
class UploadAnnotation extends AbstractAnnotation
{
    /**
     * The name of the upload field
     *
     * @var string
     */
    protected $sField = '';

    /**
     * @inheritDoc
     */
    public static function parseAnnotation($value)
    {
        $aParams = preg_split('/[\s]+/', $value, 2);
        return count($aParams) === 1 ? ['field' => $aParams[0]] :
            ['field' => $aParams[0], 'extra' => $aParams[1]];
    }

    /**
     * @inheritDoc
     * @throws AnnotationException
     */
    public function initAnnotation(array $properties)
    {
        if(count($properties) != 1 || !isset($properties['field']) ||
            !is_string($properties['field']))
        {
            throw new AnnotationException('The @upload annotation requires a property "field" of type string');
        }
        $this->sField = $properties['field'];
    }

    /**
     * @inheritDoc
     */
    public function saveValue(Metadata $xMetadata, string $sMethod = '*'): void
    {
        $xMetadata->upload($sMethod)->setValue($this->sField);
    }
}
