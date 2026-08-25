<?php

/**
 * Pie.php
 *
 * A pie to be printed in a chart.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart;

use Jaxon\Charts\Chart\Data\Pie\Series;
use Jaxon\Charts\Chart\Option\OptionTrait;
use JsonSerializable;

use function count;

class Pie implements JsonSerializable
{
    use OptionTrait;

    /**
     * @var Series|null
     */
    protected Series|null $xSeries;

    /**
     * @var array|null
     */
    protected array|null $aLabels = null;

    /**
     * The constructor
     *
     * @param array $aOptions
     */
    public function __construct(array $aOptions = [])
    {
        $this->options($aOptions);
    }

    /**
     * Set the graph labels
     *
     * @param array $aLabels
     *
     * @return static
     */
    public function labels(array $aLabels): static
    {
        $this->aLabels = $aLabels;
        return $this;
    }

    /**
     * Get the pie series
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
        $aJson = $this->xSeries === null ? [] : [
            'series' => $this->xSeries->jsonSerialize(),
        ];
        if($this->aLabels !== null)
        {
            $aJson['labels'] = $this->aLabels;
        }
        if(count($this->aOptions) > 0)
        {
            $aJson['options'] = $this->aOptions;
        }
        return $aJson;
    }
}
