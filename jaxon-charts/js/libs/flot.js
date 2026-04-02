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

    const makeChart = ({ series: { points, values }, options = {} }) => ({
        ...options,
        data: getPointValues(points, values, options),
    });

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

    const getCardOptions = (options, tooltips) => {
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

        return getCardOptions(options, tooltips);
    };

    const getChartOptions = (options, xaxes, yaxes, tooltips) => {
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

        return getCardOptions(options, tooltips);
    };

    const makeTooltipLabel = (labels, { data = null, func = null }, { label = null }) => {
        if(label !== null)
        {
            if(data !== null)
            {
                labels[label] = { data };
            }
            if(func !== null)
            {
                labels[label] = { func: dom.findFunction(func) };
            }
        }
    };

    const getChartTooltips = (charts, tooltipId) => {
        $(`#${tooltipId}`).remove();
        $(`<div id="${tooltipId}" class="flot-tooltip"></div>`).appendTo("body");

        const tooltipLabels = {};
        charts.forEach(({ series: { labels = {} }, options = {} }) =>
            makeTooltipLabel(tooltipLabels, labels, options));
        return {
            id: tooltipId,
            labels: tooltipLabels,
            show: Object.keys(tooltipLabels).length > 0,
        };
    };

    const getTooltipLabel = (tooltips, { series: { label }, datapoint: [x, y] }) => {
        const { data = null, func = null } = tooltips.labels[label];
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

        const tooltipLabel = getTooltipLabel(tooltips, item);
        if(tooltipLabel !== '')
        {
            tooltip.html(tooltipLabel)
                .css({ top: item.pageY + 5, left: item.pageX + 5 })
                .fadeIn(200);
        }
    };

    const drawCard = (wrapper, data, options, tooltips) => {
        $.plot(wrapper, data, options);

        if(tooltips.show)
        {
            wrapper.on("plothover", (event, pos, item) => showTooltip(tooltips, item));
        }

        // Fix: the card labels background is black by default. Change to white.
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
        type,
        data,
        xaxes,
        yaxes,
        options = {},
    }) => {
        if (!types.isArray(data) || data.length === 0) {
            console.error(`Charts plugin (Flot): no valid data to show in the chart with id ${selector}.`);
            return true;
        }

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

        if(type === 'pie' || type === 'donut')
        {
            const tooltips = { show: false };
            drawCard(wrapper, data[0].series, getPieOptions(options, tooltips), tooltips);
            return true;
        }

        // Create the DOM element for the tooltip.
        const tooltipId = `${selector}-flot-tooltip`;
        // Use an observer to remove the tooltip when the chart is deleted.
        observeMutations(wrapper, tooltipId);

        const tooltips = getChartTooltips(data, tooltipId);
        drawCard(wrapper, data.map(chart => makeChart(chart)),
            getChartOptions(options, xaxes, yaxes, tooltips), tooltips);

        return true;
    };
}));
