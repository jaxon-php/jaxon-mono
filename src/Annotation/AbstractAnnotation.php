<?php

/**
 * AbstractAnnotation.php
 *
 * Common functions for Jaxon annotations.
 *
 * @package jaxon-annotations
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2022 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-annotations
 */

namespace Jaxon\Annotations\Annotation;

use Jaxon\Annotations\AnnotationReader;
use mindplay\annotations\Annotation;
use mindplay\annotations\IAnnotationParser;

abstract class AbstractAnnotation extends Annotation implements IAnnotationParser
{
    /**
     * @var AnnotationReader
     */
    protected $xReader;

    /**
     * @var mixed
     */
    protected $xPrevValue = null;

    /**
     * @param AnnotationReader $xReader
     *
     * @return void
     */
    public function setReader(AnnotationReader $xReader): void
    {
        $this->xReader = $xReader;
    }

    /**
     * Set the attribute previous value
     *
     * @param mixed $xPrevValue The previous value of the attribute
     *
     * @return void
     */
    public function setPrevValue($xPrevValue)
    {
        $this->xPrevValue = $xPrevValue;
    }

    /**
     * Get the annotation name
     *
     * This is usually the corresponding option name in the Jaxon config.
     *
     * @return string
     */
    abstract public function getName(): string;

    /**
     * Get the annotation value
     *
     * For attributes with multiple values, the previous value needs to be merged with the current.
     *
     * @return mixed
     */
    abstract public function getValue();
}
