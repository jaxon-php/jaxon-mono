<?php

/**
 * Graph.php
 *
 * A graph to be printed in a chart.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart;

use Jaxon\Charts\Chart\Data\Graph\Series;
use Jaxon\Charts\Chart\Option\OptionTrait;
use JsonSerializable;

use function count;

class Graph implements JsonSerializable
{
    use OptionTrait;

    /**
     * @var Series|null
     */
    protected Series|null $xSeries = null;

    /**
     * @var string|null
     */
    protected string|null $sLabel = null;

    /**
     * @param string $sType
     * @param bool $bFill
     */
    public function __construct(private string $sType, private bool $bFill)
    {}

    /**
     * Set the graph label
     *
     * @param string $sLabel
     *
     * @return static
     */
    public function label(string $sLabel): static
    {
        $this->sLabel = $sLabel;
        return $this;
    }

    /**
     * Get the graph series
     *
     * @return Series
     */
    public function series(): Series
    {
        return $this->xSeries ??= new Series();
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
        if($this->xSeries === null)
        {
            return [];
        }

        $aJson = [
            'type' => $this->sType,
            'fill' => $this->bFill,
            'series' => $this->xSeries->jsonSerialize(),
        ];
        if($this->sLabel !== null)
        {
            $aJson['label'] = $this->sLabel;
        }
        if(count($this->aOptions) > 0)
        {
            $aJson['options'] = $this->aOptions;
        }
        return $aJson;
    }
}
