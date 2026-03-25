/*
 * Jaxon Flot plugin
 */

/** global: jaxon */
jaxon.dom.ready(() => jaxon.chart.register('flot', (self, utils) => {
    const { dom, types } = jaxon.utils;

    const wrapperHtml = (container, wrapperId, width, height) => {
        if(width === '')
        {
            width = container.css('width');
        }
        if(height === '')
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

    const getPointValues = (points, { data = null, func = null }) => {
        if(data !== null)
        {
            return getPoints(points).map(point => [point, data[point]]);
        }

        if(func !== null)
        {
            func = dom.findFunction(func);
            return getPoints(points).map(point => [point, func(point)]);
        }

        return [];
    };

    const makeGraph = ({ series: { points, values }, options = {} }) => ({
        ...options,
        data: getPointValues(points, values),
    });

    const makeTooltip = (tooltips, { data = null, func = null }, { label = null }) => {
        if(label !== null)
        {
            if(data !== null)
            {
                tooltips[label] = { data };
            }
            if(func !== null)
            {
                tooltips[label] = { func: dom.findFunction(func) };
            }
        }
        return tooltips;
    };

    const getTooltipLabel = (tooltips, { series: { label }, datapoint: [x, y] }) => {
        const { data = null, func = null } = tooltips[label];
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

    const showTooltip = (tooltips, tooltipId, item) => {
        const tooltip = $(`#${tooltipId}`);
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

    const getCardOptions = (options, xaxes, yaxes, showLabels) => {
        if(types.isArray(xaxes))
        {
            // Note: When length > 1, the option name is different.
            if(xaxes.length === 1)
            {
                options.xaxis = makeAxis(xaxes[0]);
            }
            if(xaxes.length > 1)
            {
                options.xaxes = xaxes.map(values => makeAxis(values));
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
                options.yaxes = yaxes.map(values => makeAxis(values));
            }
        }

        if(showLabels)
        {
            options.grid = { ...options.grid, hoverable: true };
        }

        return options;
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
        size: { width = '', height = '' },
        graphs,
        pies,
        xaxes,
        yaxes,
        options = {},
    }) => {
        const container = $(`#${selector}`);
        if(!container)
        {
            console.error(`Flot plugin: unable to find the DOM element with id ${selector}.`);
            return;
        }

        // A wrapper is added so the graph destruction can be detected using its removal.
        const wrapperId = `${selector}-wrapper`;
        container.html(wrapperHtml(container, wrapperId, width, height));
        const wrapper = $(`#${wrapperId}`);

        if (types.isArray(pies)) {
            if (pies.length > 0) {
                if (types.isString(formatter = options.series?.pie?.label?.formatter)) {
                    options.series.pie.label.formatter = dom.findFunction(formatter);
                }
                const [{ series: pie }] = pies;
                $.plot(wrapper, pie, options);
            }
            return;
        }

        if (!types.isArray(graphs) || graphs.length === 0) {
            console.error(`Flot plugin: no valid data to show in the graph with id ${selector}.`);
            return;
        }

        // Create the DOM element for the tooltip.
        const tooltipId = `${selector}-flot-tooltip`;
        $(`#${tooltipId}`).remove();
        $(`<div id="${tooltipId}" class="flot-tooltip"></div>`).appendTo("body");

        // Use an observer to remove the tooltip when the graph is deleted.
        observeMutations(wrapper, tooltipId);

        const tooltips = graphs.reduce((_tooltips, { series: { labels = {} }, options }) =>
            makeTooltip(_tooltips, labels, options), {});
        const showLabels = Object.keys(tooltips).length > 0;

        $.plot(wrapper, graphs.map(graph => makeGraph(graph)),
            getCardOptions(options, xaxes, yaxes, showLabels));

        // Labels
        if(showLabels)
        {
            wrapper.on("plothover", (event, pos, item) => showTooltip(tooltips, tooltipId, item));
        }
        return true;
    };
}));
