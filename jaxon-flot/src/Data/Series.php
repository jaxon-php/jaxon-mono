<?php

/**
 * Series.php
 *
 * Contains data to be printed in a graph.
 *
 * @package jaxon-flot
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2017 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-flot
 */

namespace Jaxon\Flot\Data;

use JsonSerializable;

use function array_filter;
use function count;

class Series implements JsonSerializable
{
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
     * The points labels
     *
     * @var array
     */
    protected $aLabels = [];

    /**
     * Add a point to the series.
     *
     * @param float $iXaxis The point on the X axis
     * @param float|string $xValue The point value
     * @param string $sLabel The point label
     *
     * @return static
     */
    public function point(float $iXaxis, float|string $xValue, string $sLabel = ''): static
    {
        $this->aPoints[] = $iXaxis;
        if(!isset($this->aValues['data']))
        {
            $this->aValues['data'] = [];
        }
        $this->aValues['data'][$iXaxis] = $xValue;
        if($sLabel !== '')
        {
            if(!isset($this->aLabels['data']))
            {
                $this->aLabels['data'] = [];
            }
            $this->aLabels['data'][$iXaxis] = $sLabel;
        }

        return $this;
    }

    /**
     * Add an array of points to the series.
     *
     * @param array $aPoints
     *
     * @return int
     */
    public function points(array $aPoints): int
    {
        $aPoints = array_filter($aPoints, fn(array $aPoint) =>
            count($aPoint) === 2 || count($aPoint) === 3);
        foreach($aPoints as $aPoint)
        {
            $this->point($aPoint[0], $aPoint[1], $aPoint[2] ?? '');
        }

        return count($this->aPoints);
    }

    /**
     * Add points to the graph series using an expression.
     *
     * @param float $iStart The first point
     * @param float $iEnd The last point
     * @param float $iStep The step between next points
     * @param string $sJsValue The javascript function to get points values
     * @param string $sJsLabel The javascript function to get points labels
     *
     * The first three parameters are used in a for loop.
     * The first javascript function takes the x value as parameter, and returns the corresponding point value.
     * The second javascript function takes the x and y values and the series label as parameters,
     * and returns the corresponding point label.
     *
     * @return int
     */
    public function expr(float $iStart, float $iEnd, float $iStep, string $sJsValue, string $sJsLabel = ''): int
    {
        for($x = $iStart; $x < $iEnd; $x += $iStep)
        {
            $this->aPoints[] = $x;
        }
        $this->aValues['func'] = $sJsValue;
        if($sJsLabel !== '')
        {
            $this->aLabels['func'] = $sJsLabel;
        }

        return count($this->aPoints);
    }

    /**
     * Convert this object to array.
     *
     * This is a method of the JsonSerializable interface.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'points' => $this->aPoints,
            'values' => $this->aValues,
            'labels' => $this->aLabels,
        ];
    }
}
