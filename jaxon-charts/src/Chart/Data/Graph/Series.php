<?php

/**
 * Series.php
 *
 * Contains data to be printed in a graph.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart\Data\Graph;

use Jaxon\Charts\Chart\Data\Labels;
use Jaxon\Charts\Chart\Data\Loop;
use JsonSerializable;

use function array_filter;
use function array_values;
use function count;

class Series implements JsonSerializable
{
    use Labels;

    /**
     * The points
     *
     * @var array
     */
    protected $aPoints = [];

    /**
     * The points values
     *
     * @var array
     */
    protected $aValues = [];

    /**
     * Add a point to the series.
     *
     * @param float $iXaxis The point on the X axis
     * @param float|string $xValue The point value
     * @param string $sTooltip The point tooltip
     *
     * @return static
     */
    public function point(float $iXaxis, float|string $xValue, string $sTooltip = ''): static
    {
        $this->aPoints[] = $iXaxis;
        if(!isset($this->aValues['data']))
        {
            $this->aValues['data'] = [];
        }
        $this->aValues['data'][$iXaxis] = $xValue;
        if($sTooltip !== '')
        {
            if(!isset($this->aLabels['data']))
            {
                $this->aLabels['data'] = [];
            }
            $this->aLabels['data'][$iXaxis] = $sTooltip;
        }
        return $this;
    }

    /**
     * Add an array of points to the series.
     *
     * @param array $aPoints
     *
     * @return static
     */
    public function points(array $aPoints): static
    {
        $aPoints = array_filter($aPoints, fn(array $aPoint) =>
            count($aPoint) === 2 || count($aPoint) === 3);
        foreach(array_values($aPoints) as $aPoint)
        {
            $this->point($aPoint[0], $aPoint[1], $aPoint[2] ?? '');
        }
        return $this;
    }

    /**
     * Add points to the graph series using a function expression.
     *
     * @param string $sJsValues The javascript function to get points values
     * @param Loop $loop
     *
     * @return static
     */
    public function func(string $sJsValues, Loop $loop): static
    {
        $this->aValues['func'] = $sJsValues;
        $this->aPoints = $loop->points();
        return $this;
    }

    /**
     * Convert this object to array.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return count($this->aLabels) > 0 ? [
            'points' => $this->aPoints,
            'values' => $this->aValues,
            'tooltips' => $this->aLabels,
        ] : [
            'points' => $this->aPoints,
            'values' => $this->aValues,
        ];
    }
}
