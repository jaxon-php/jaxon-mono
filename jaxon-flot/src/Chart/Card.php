<?php

/**
 * Card.php
 *
 * A card containing one or more graphs.
 *
 * @package jaxon-flot
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2017 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-flot
 */

namespace Jaxon\Flot\Chart;

use Jaxon\Flot\Data\Ticks;
use JsonSerializable;

use function count;
use function trim;

class Card implements JsonSerializable
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
     * @var array<Graph>
     */
    protected array $aGraphs = [];

    /**
     * The pie graph
     *
     * @var Pie|null
     */
    protected Pie|null $xPie = null;

    /**
     * The card options
     *
     * @var array
     */
    protected array $aOptions = [];

    /**
     * The card width
     *
     * @var string
     */
    protected string $sWidth = '';

    /**
     * The card height
     *
     * @var string
     */
    protected string $sHeight = '';

    /**
     * The card X axis
     *
     * @var array<Ticks>
     */
    protected array $aAxesX = [];

    /**
     * The card Y axis
     *
     * @var array<Ticks>
     */
    protected array $aAxesY = [];

    /**
     * The constructor.
     *
     * @param string $sSelector The jQuery selector
     * @param array $aOptions
     */
    public function __construct(string $sSelector, array $aOptions = [])
    {
        $this->sSelector = trim($sSelector, " \t");
        $this->options($aOptions);
    }

    /**
     * Set the card options.
     *
     * @param array $aOptions The card options
     *
     * @return static
     */
    public function options(array $aOptions): static
    {
        $this->aOptions = [...$this->aOptions, ...$aOptions];
        return $this;
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
     * Add a pie to the card.
     *
     * @return Pie
     */
    public function pie(): Pie
    {
        return $this->xPie ??= new Pie();
    }

    /**
     * Add a new graph to the card.
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
        $this->aAxesX[] = $xTicks;
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
        $this->aAxesY[] = $xTicks;
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
            'size' => ['width' => $this->sWidth, 'height' => $this->sHeight],
            'options' => $this->aOptions,
        ];

        if($this->xPie !== null)
        {
            $aJson['pie'] = $this->xPie;
            return $aJson;
        }

        $aJson['graphs'] = $this->aGraphs;
        if(count($this->aAxesX) > 0)
        {
            $aJson['xaxes'] = $this->aAxesX;
        }
        if(count($this->aAxesY) > 0)
        {
            $aJson['yaxes'] = $this->aAxesY;
        }

        return $aJson;
    }
}
