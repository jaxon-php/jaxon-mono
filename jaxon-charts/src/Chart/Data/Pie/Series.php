<?php

/**
 * Series.php
 *
 * Contains data to be printed in a pie chart.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart\Data\Pie;

use JsonSerializable;

use function array_filter;
use function array_values;
use function count;

class Series implements JsonSerializable
{
    /**
     * The slices
     *
     * @var array
     */
    protected $aSlices = [];

    /**
     * Add a slice to the series.
     *
     * @param float $fValue The slice value
     * @param string $sLabel The slice label
     *
     * @return static
     */
    public function slice(float $fValue, string $sLabel = ''): static
    {
        $this->aSlices[] = [
            'data' => $fValue,
            'label' => $sLabel,
        ];
        return $this;
    }

    /**
     * Add an array of slices to the series.
     *
     * @param array $aSlices
     *
     * @return static
     */
    public function slices(array $aSlices): static
    {
        $aSlices = array_filter($aSlices, fn(array $aSlice) => count($aSlice) === 2);
        foreach(array_values($aSlices) as $aSlice)
        {
            $this->slice($aSlice[0], $aSlice[1]);
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
        return $this->aSlices;
    }
}
