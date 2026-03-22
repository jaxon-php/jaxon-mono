<?php

/**
 * Ticks.php
 *
 * Contains data to be printed on the card axis.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart\Data\Axis;

use JsonSerializable;

use function array_filter;
use function array_values;
use function count;

class Ticks implements JsonSerializable
{
    /**
     * The points
     *
     * @var array
     */
    protected $aPoints = [];

    /**
     * The points labels
     *
     * @var array
     */
    protected $aLabels = [];

    /**
     * Add a point to the ticks.
     *
     * @param float $iXaxis The point on the X axis
     * @param string $sLabel The point label on the X axis
     *
     * @return static
     */
    public function point(float $iXaxis, string $sLabel): static
    {
        $this->aPoints[] = $iXaxis;
        if(!isset($this->aLabels['data']))
        {
            $this->aLabels['data'] = [];
        }
        $this->aLabels['data'][$iXaxis] = $sLabel;
        return $this;
    }

    /**
     * Add an array of points to the ticks.
     *
     * @param array $aPoints The points to be added
     *
     * @return static
     */
    public function points(array $aPoints): static
    {
        $aPoints = array_filter($aPoints, fn(array $aPoint) =>
            count($aPoint) === 1 || count($aPoint) === 2);
        foreach(array_values($aPoints) as $aPoint)
        {
            $this->point($aPoint[0], $aPoint[1] ?? '');
        }
        return $this;
    }

    /**
     * Add points to the ticks using a loop expression.
     *
     * @param float $iStart The first point
     * @param float $iEnd The last point
     * @param float|string $xStep The step between next points
     * @param string $sJsLabel The javascript function to get points labels
     *
     * The first three parameters are used in a for loop.
     * The step can be either a float value or a javascript function.
     * The javascript function takes the x value as parameter, and returns the corresponding point label.
     *
     * @return static
     */
    public function loop(float $iStart, float $iEnd, float|string $xStep, string $sJsLabel = ''): static
    {
        $this->aPoints = [
            'start' => $iStart,
            'end' => $iEnd,
            'step' => $xStep,
        ];
        if($sJsLabel !== '')
        {
            $this->aLabels['func'] = $sJsLabel;
        }
        return $this;
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
        $aJson = [];
        if(count($this->aPoints) > 0)
        {
            $aJson['points'] = $this->aPoints;
            if(count($this->aLabels) > 0)
            {
                $aJson['labels'] = $this->aLabels;
            }
        }
        return $aJson;
    }
}
