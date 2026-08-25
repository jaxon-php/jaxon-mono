<?php

/**
 * Chart.php
 *
 * A chart containing one or more graphs.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart;

use Jaxon\Charts\Chart\Data\Loop;
use Jaxon\Charts\Chart\Option\OptionTrait;
use JsonSerializable;

use function count;
use function trim;

class Chart implements JsonSerializable
{
    use OptionTrait;

    /**
     * The HTML element selector
     *
     * @var string
     */
    protected string $sSelector;

    /**
     * The graph charts
     *
     * @var array<Graph>
     */
    protected array $aGraphs = [];

    /**
     * The pie charts
     *
     * @var array<Pie>
     */
    protected array $aPies = [];

    /**
     * The chart width
     *
     * @var string
     */
    protected string $sWidth = '';

    /**
     * The chart height
     *
     * @var string
     */
    protected string $sHeight = '';

    /**
     * The chart X axes
     *
     * @var array<Axis>
     */
    protected array $aAxesX = [];

    /**
     * The chart Y axes
     *
     * @var array<Axis>
     */
    protected array $aAxesY = [];

    /**
     * @param string $sSelector The jQuery selector
     * @param array $aOptions
     */
    public function __construct(string $sSelector, array $aOptions = [])
    {
        $this->sSelector = trim($sSelector, " \t");
        $this->options($aOptions);
    }

    /**
     * Get a loop expression.
     *
     * @param float $iStart The first point
     * @param float $iEnd The last point
     * @param float|string $xStep The step between next points
     *
     * @return Loop
     */
    public function loop(float $iStart, float $iEnd, float|string $xStep): Loop
    {
        return new Loop($iStart, $iEnd, $xStep);
    }

    /**
     * Set the container width.
     *
     * @param string $sWidth The container width
     *
     * @return static
     */
    public function width(string $sWidth): static
    {
        $this->sWidth = trim($sWidth, " \t");
        return $this;
    }

    /**
     * Set the container height.
     *
     * @param string $sHeight The container height
     *
     * @return static
     */
    public function height(string $sHeight): static
    {
        $this->sHeight = trim($sHeight, " \t");
        return $this;
    }

    /**
     * Add a pie to the chart.
     *
     * @param array $aOptions
     *
     * @return Pie
     */
    public function pie(array $aOptions = []): Pie
    {
        $xPie = new Pie($aOptions);
        $this->aPies[] = $xPie;
        return $xPie;
    }

    /**
     * Add a new graph to the chart.
     *
     * @param string $sType
     * @param bool $bFill
     *
     * @return Graph
     */
    public function graph(string $sType, bool $bFill = false): Graph
    {
        $xGraph = new Graph($sType, $bFill);
        $this->aGraphs[] = $xGraph;
        return $xGraph;
    }

    /**
     * Add an X axis to the chart.
     *
     * @param array $aOptions
     *
     * @return Axis
     */
    public function xaxis(array $aOptions = []): Axis
    {
        $xAxis = new Axis($aOptions);
        $this->aAxesX[] = $xAxis;
        return $xAxis;
    }

    /**
     * Add a Y axis to the chart.
     *
     * @param array $aOptions
     *
     * @return Axis
     */
    public function yaxis(array $aOptions = []): Axis
    {
        $xAxis = new Axis($aOptions);
        $this->aAxesY[] = $xAxis;
        return $xAxis;
    }

    /**
     * @param array $aGraphs
     *
     * @return array
     */
    private function makeConfig(array $aGraphs): array
    {
        $aOptions = [
            'selector' => $this->sSelector,
            'size' => ['width' => $this->sWidth, 'height' => $this->sHeight],
            ...$aGraphs,
        ];
        if(count($this->aOptions) > 0)
        {
            $aOptions['options'] = $this->aOptions;
        }
        return $aOptions;
    }

    /**
     * Convert this object to an array for json format.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        if(count($this->aPies) > 0)
        {
            return $this->makeConfig(['pie' => $this->aPies[0]]);
        }

        $aAxesX = count($this->aAxesX) > 0 ? ['xaxes' => $this->aAxesX] : [];
        $aAxesY = count($this->aAxesY) > 0 ? ['yaxes' => $this->aAxesY] : [];
        return [
            ...$this->makeConfig(['graphs' => $this->aGraphs]),
            ...$aAxesX,
            ...$aAxesY,
        ];
    }
}
