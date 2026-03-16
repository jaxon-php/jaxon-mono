<?php

/**
 * Graph.php
 *
 * A graph to be printed in a plot.
 *
 * @package jaxon-flot
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2017 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-flot
 */

namespace Jaxon\Flot\Plot;

use JsonSerializable;
use Jaxon\Flot\Data\Series;

class Graph implements JsonSerializable
{
    /**
     * @var Series
     */
    public Series $xSeries;

    /**
     * The constructor.
     *
     * @param array $aOptions The graph options, as defined by the Flot library
     */
    public function __construct(public array $aOptions = [])
    {
        $this->xSeries = new Series();
    }

    /**
     * Get this plot dataset
     *
     * @return Series
     */
    public function series(): Series
    {
        return $this->xSeries;
    }

    /**
     * Set the graph options.
     *
     * @param array $aOptions The graph options
     *
     * @return static
     */
    public function setOptions(array $aOptions): static
    {
        $this->aOptions = [...$this->aOptions, ...$aOptions];
        return $this;
    }

    /**
     * Convert this object to another object more suitable for json format.
     *
     * This is a method of the JsonSerializable interface.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            ...$this->xSeries->jsonSerialize(),
            'options' => $this->aOptions,
        ];
    }
}
