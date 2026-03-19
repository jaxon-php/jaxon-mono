/*
 * Jaxon Flot plugin
 */

/** global: jaxon */
jaxon.dom.ready(() => {
    jaxon.register("flot.plot", ({
        plot: {
            selector,
            graphs,
            pie,
            size: { width: cWidth = '', height: cHeight = '' },
            xaxis,
            xaxes,
            yaxis,
            yaxes,
            options = {},
        }
    }) => {
        const container = $(`#${selector}`);
        if(!container)
        {
            console.error(`Flot plugin: unable to find the DOM element with id ${selector}.`);
            return;
        }

        // A wrapper is added so the graph destruction can be detected using its removal.
        // Set the wrapper size.
        const width = cWidth !== '' ? cWidth : container.css('width');
        const height = cHeight !== '' ? cHeight : container.css('height');
        const wrapperId = `${selector}-wrapper`;
        container.html(`<div id="${wrapperId}" style="width:${width}; height:${height};"></div>`);
        const wrapper = $(`#${wrapperId}`);

        const { dom, types } = jaxon.utils;

        if (types.isArray(pie) && pie.length > 0) {
            if (types.isString(options.series?.pie?.label?.formatter)) {
                options.series.pie.label.formatter = dom.findFunction(options.series.pie.label.formatter);
            }
            $.plot(wrapper, pie, options);
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
                }
            });
        });
        observer.observe(document.body, { subtree: true, childList: true });

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

        const makeAxis = (values) => {
            const { points = [], labels = {}, options = {} } = values;
            if(types.isString(options.tickFormatter))
            {
                options.tickFormatter = dom.findFunction(options.tickFormatter);
            }
            return points.length === 0 ? options : {
                ...options,
                ticks: getPointValues(points, labels),
            };
        };

        if(types.isObject(xaxis))
        {
            options.xaxis = makeAxis(xaxis);
        }
        if(types.isArray(xaxes) && xaxes.length > 0)
        {
            options.xaxes = xaxes.map(values => makeAxis(values));
        }
        if(types.isObject(yaxis))
        {
            options.yaxis = makeAxis(yaxis);
        }
        if(types.isArray(yaxes) && yaxes.length > 0)
        {
            options.yaxes = yaxes.map(values => makeAxis(values));
        }

        let showLabels = false;
        const tooltips = {};
        const makeGraph = ({ points, values, labels = {}, options }) => {
            const graph = options || {};
            graph.data = getPointValues(points, values);
            const { label } = options;
            const { data = null, func = null } = labels;
            if(label !== undefined && (data !== null || func !== null))
            {
                showLabels = true;
                tooltips[label] = data !== null ? { data } : {
                    func: dom.findFunction(func),
                };
            }
            return graph;
        };

        const makeTooltipLabel = ({ series: { label }, datapoint: [x, y] }) => {
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

        if(showLabels)
        {
            options.grid = { ...options.grid, hoverable: true };
        }
        $.plot(wrapper, graphs.map(graph => makeGraph(graph)), options);

        // Labels
        if(!showLabels)
        {
            return true;
        }

        wrapper.bind("plothover", function (event, pos, item) {
            const tooltip = $(`#${tooltipId}`);
            if(!item)
            {
                tooltip.hide();
                return;
            }

            const tooltipLabel = makeTooltipLabel(item);
            if(tooltipLabel !== '')
            {
                tooltip.html(tooltipLabel)
                    .css({ top: item.pageY + 5, left: item.pageX + 5 })
                    .fadeIn(200);
            }
        });
        return true;
    });
});
