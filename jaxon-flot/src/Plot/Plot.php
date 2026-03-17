<?php

/**
 * Plot.php
 *
 * A plot containing one or more graphs.
 *
 * @package jaxon-flot
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2017 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-flot
 */

namespace Jaxon\Flot\Plot;

use Jaxon\Flot\Data\Ticks;
use JsonSerializable;

use function count;
use function trim;

class Plot implements JsonSerializable
{
    /**
     * The HTML element selector
     *
     * @var string
     */
    protected string $sSelector;

    /**
     * The graphs
     *
     * @var array
     */
    protected array $aGraphs = [];

    /**
     * The plot options
     *
     * @var array
     */
    protected array $aOptions;

    /**
     * The plot width
     *
     * @var string
     */
    protected string $sWidth = '';

    /**
     * The plot height
     *
     * @var string
     */
    protected string $sHeight = '';

    /**
     * The plot X axis
     *
     * @var array<Ticks>
     */
    protected array $aAxisX = [];

    /**
     * The plot Y axis
     *
     * @var array<Ticks>
     */
    protected array $aAxisY = [];

    /**
     * The constructor.
     *
     * @param string $sSelector The jQuery selector
     */
    public function __construct(string $sSelector)
    {
        $this->sSelector = trim($sSelector, " \t");
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
     * Add a new graph to the plot.
     *
     * @param array $aOptions The graph options
     *
     * @return Graph
     */
    public function graph(array $aOptions = []): Graph
    {
        $xGraph = new Graph($aOptions);
        $this->aGraphs[] = $xGraph;
        return $xGraph;
    }

    /**
     * Get the graph X axis.
     *
     * @return Ticks
     */
    public function xaxis(): Ticks
    {
        $xTicks = new Ticks();
        $this->aAxisX[] = $xTicks;
        return $xTicks;
    }

    /**
     * Get the graph Y axis.
     *
     * @return Ticks
     */
    public function yaxis(): Ticks
    {
        $xTicks = new Ticks();
        $this->aAxisY[] = $xTicks;
        return $xTicks;
    }

    /**
     * Convert this object to an array for json format.
     *
     * This is a method of the JsonSerializable interface.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $aJson = [
            'selector' => $this->sSelector,
            'graphs' => $this->aGraphs,
            'size' => ['width' => $this->sWidth, 'height' => $this->sHeight],
        ];

        // !!Note: The names when count > 1 are different. That's how The Flot library works.
        switch(count($this->aAxisX))
        {
        case 0:
            break;
        case 1:
            $aJson['xaxis'] = $this->aAxisX[0];
            break;
        default:
            $aJson['xaxes'] = $this->aAxisX;
        }
        switch(count($this->aAxisY))
        {
        case 0:
            break;
        case 1:
            $aJson['yaxis'] = $this->aAxisY[0];
            break;
        default:
            $aJson['yaxes'] = $this->aAxisY;
        }

        return $aJson;
    }
}
