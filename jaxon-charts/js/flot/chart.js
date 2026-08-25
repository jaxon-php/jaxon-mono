/*
 * Jaxon Charts plugin: Flot library support
 */

/** global: jaxon */
jaxon.dom.ready(() => jaxon.chart.register('flot', (self, utils) => {
    const { dom, types } = jaxon.utils;

    const wrapperHtml = (container, wrapperId, { width, height } = {}) => {
        if(!width)
        {
            width = container.css('width');
        }
        if(!height)
        {
            height = container.css('height');
        }
        return `<div id="${wrapperId}" style="width:${width}; height:${height};"></div>`;
    };

    const getPoints = (points) => {
        if(types.isArray(points))
        {
            return points;
        }
        if(!types.isObject(points))
        {
            return [];
        }

        const { start, end, step } = points;
        if(!types.isString(step))
        {
            const vals = [];
            for(let val = start; val < end; val += step)
            {
                vals.push(val);
            }
            return vals;
        }

        const func = dom.findFunction(step);
        if(!func)
        {
            return [];
        }
        const vals = [];
        for(let val = start; val < end; val += func(val))
        {
            vals.push(val);
        }
        return vals;
    };

    const getPointValues = (points, { data = null, func = null }, options = {}) => {
        const { bars: { show, horizontal } = {} } = options;
        // Swap the x and y values when drawing horizontal bars.
        const pointValue = (x, y) => show && horizontal ? [y, x] : [x, y];

        if(data !== null)
        {
            return getPoints(points).map(point => pointValue(point, data[point]));
        }
        if(func !== null)
        {
            func = dom.findFunction(func);
            return getPoints(points).map(point => pointValue(point, func(point)));
        }
        return [];
    };

    const getPieConfig = ({ series, labels }) => {
        return series.map(({ value }, index) => ({ data: value, label: labels[index] ?? '' }));
    };

    const getGraphConfig = (graphs) => {
        return graphs.map(({
            type,
            fill,
            series: { points, values },
            label,
            options = {},
        }) => {
            if (['line', 'bar', 'point'].includes(type))
            {
                options = {
                    ...options,
                    [`${type}s`]: { show: true, fill, ...(options[`${type}s`] ?? {}) },
                };
            }
            // Copy the label into the options.
            if(label !== undefined && options.label === undefined)
            {
                options.label = label;
            }
            return {
                ...options,
                data: getPointValues(points, values, options),
            };
        });
    };

    const makeAxis = ({ ticks: { points = [], labels = {} } = {}, options = {} }) => {
        if(types.isString(options.tickFormatter))
        {
            options.tickFormatter = dom.findFunction(options.tickFormatter);
        }
        return points.length === 0 ? options : {
            ...options,
            ticks: getPointValues(points, labels),
        };
    };

    const showLegend = (options) => options.legend?.show ?? false;

    const getChartOptions = (options, tooltips) => {
        if (types.isString(formatter = options.legend?.labelFormatter)) {
            options.legend.labelFormatter = dom.findFunction(formatter);
        }
        if(tooltips.show)
        {
            options.grid = { ...options.grid, hoverable: true };
        }
        if(showLegend(options) && types.isString(options.legend?.container))
        {
            // The Flot library expects a Javascript (not jQuery) DOM element here.
            options.legend.container = document.getElementById(options.legend.container);
        }
        return options;
    };

    const getPieOptions = (options, tooltips) => {
        if (types.isString(formatter = options.series?.pie?.label?.formatter)) {
            options.series.pie.label.formatter = dom.findFunction(formatter);
        }

        return getChartOptions(options, tooltips);
    };

    const getGraphOptions = (options, xaxes, yaxes, tooltips) => {
        if (types.isString(formatter = options.series?.label?.labelFormatter)) {
            options.series.label.labelFormatter = dom.findFunction(formatter);
        }
        if(types.isArray(xaxes))
        {
            // Note: When length > 1, the option name is different.
            if(xaxes.length === 1)
            {
                options.xaxis = makeAxis(xaxes[0]);
            }
            if(xaxes.length > 1)
            {
                options.xaxes = xaxes.map(xaxis => makeAxis(xaxis));
            }
        }
        if(types.isArray(yaxes))
        {
            // Note: When length > 1, the option name is different.
            if(yaxes.length === 1)
            {
                options.yaxis = makeAxis(yaxes[0]);
            }
            if(yaxes.length > 1)
            {
                options.yaxes = yaxes.map(yaxis => makeAxis(yaxis));
            }
        }

        return getChartOptions(options, tooltips);
    };

    const makeTooltipLabel = (labels, { data = null, func = null }, index) => {
        if(data !== null)
        {
            labels[index] = { data };
        }
        if(func !== null)
        {
            labels[index] = { func: dom.findFunction(func) };
        }
    };

    /**
     * Save the tooltips in an array
     *
     * @param {array} graphs
     * @param {string} tooltipId
     *
     * @returns {object}
     */
    const getChartTooltips = (graphs, tooltipId) => {
        $(`#${tooltipId}`).remove();
        $(`<div id="${tooltipId}" class="flot-tooltip"></div>`).appendTo("body");

        const tooltipLabels = [];
        graphs.forEach(({ series: { tooltips = {} } }, index) =>
            makeTooltipLabel(tooltipLabels, tooltips, index));
        return {
            id: tooltipId,
            labels: tooltipLabels,
            show: tooltipLabels.length > 0,
        };
    };

    /**
     * Make a tooltip label using the saved array
     *
     * @returns {string}
     */
    const getTooltipLabel = ({ data = null, func = null } = {}, label, [x, y]) => {
        if(data !== null && data[x] !== undefined)
        {
            return data[x];
        }
        if(func !== null)
        {
            return func(x, y, label);
        }
        return '';
    };

    const showTooltip = (tooltips, item) => {
        const tooltip = $(`#${tooltips.id}`);
        if(!item)
        {
            tooltip.hide();
            return;
        }

        const { series: { label }, seriesIndex: index, datapoint } = item;
        const tooltipLabel = getTooltipLabel(tooltips.labels[index], label, datapoint);
        if(tooltipLabel !== '')
        {
            tooltip.html(tooltipLabel)
                .css({ top: item.pageY + 5, left: item.pageX + 5 })
                .fadeIn(200);
        }
    };

    const drawChart = (wrapper, data, options, tooltips) => {
        if (data === null) {
            return;
        }

        $.plot(wrapper, data, options);

        if(tooltips.show)
        {
            wrapper.on("plothover", (event, pos, item) => showTooltip(tooltips, item));
        }

        // Fix: the chart labels background is black by default. Change to white.
        if(showLegend(options))
        {
            const legendContainer = options.legend.container ?? wrapper;
            $('rect.background', legendContainer).attr('fill', '#ffffff');
        }
    };

    const mutationObservers = {};
    const observeMutations = (wrapper, tooltipId) => {
        const observer = new MutationObserver((mutations) => {
            const target = wrapper.get(0); // The actual DOM element.
            // check for removed target
            mutations.forEach((mutation) => {
                const removedNodes = Array.from(mutation.removedNodes);
                // Directly removed.
                const directMatch = removedNodes.indexOf(target) > -1;
                // Removed through a removed parent.
                const parentMatch = removedNodes.some(parent => parent.contains(target));
                if(directMatch || parentMatch)
                {
                    $(`#${tooltipId}`).remove();
                    const mutationObserver = mutationObservers[tooltipId];
                    if(mutationObserver !== undefined)
                    {
                        // Disconnect the observer;
                        mutationObserver.disconnect();
                        mutationObservers[tooltipId] = undefined;
                    }
                }
            });
        });
        observer.observe(document.body, { subtree: true, childList: true });
        // Save the observer.
        mutationObservers[tooltipId] = observer;
    };

    self.show = ({
        selector,
        size,
        pie,
        graphs,
        xaxes,
        yaxes,
        options = {},
    }) => {
        const container = $(`#${selector}`);
        if(!container)
        {
            console.error(`Charts plugin (Flot): unable to find the DOM element with id ${selector}.`);
            return true;
        }

        // A wrapper is added so the chart destruction can be detected using its removal.
        const wrapperId = `${selector}-wrapper`;
        container.html(wrapperHtml(container, wrapperId, size));
        const wrapper = $(`#${wrapperId}`);

        if(pie && types.isObject(pie))
        {
            const tooltips = { show: false };
            const pieOptions = getPieOptions(options, tooltips);
            drawChart(wrapper, getPieConfig(pie), pieOptions, tooltips);
            return true;
        }

        if(graphs && types.isArray(graphs) && graphs.length > 0)
        {
            // Create the DOM element for the tooltip.
            const tooltipId = `${selector}-flot-tooltip`;
            // Use an observer to remove the tooltip when the chart is deleted.
            observeMutations(wrapper, tooltipId);

            const tooltips = getChartTooltips(graphs, tooltipId);
            const graphOptions = getGraphOptions(options, xaxes, yaxes, tooltips);
            drawChart(wrapper, getGraphConfig(graphs), graphOptions, tooltips);
            return true;
        }

        console.error(`Charts plugin (ChartJs): no valid data to show in the chart with id ${selector}.`);
        return true;
    };
}));
