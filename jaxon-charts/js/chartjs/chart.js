/*
 * Jaxon Charts plugin: Charts library support
 */

/** global: jaxon */
jaxon.dom.ready(() => jaxon.chart.register('chartjs', (self, utils) => {
    const { dom, types } = jaxon.utils;
    const Utils = ChartUtils.init();

    const fallbackContent = 'Unable to draw a chart in the canvas';
    const wrapperHtml = (container, wrapperId, { width, height } = {}) => {
        if(!width)
        {
            width = container.css('width');
        }
        if(!height)
        {
            height = container.css('height');
        }
        return `
<div style="width:${width}; height:${height};">
    <canvas id="${wrapperId}" aria-label="${fallbackContent}" role="img"></canvas>
</div>
`;
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

    const makeAxis = ({ ticks: { points = [], labels = {} } = {} }, options = {}) => {
        if(types.isString(options.tickFormatter))
        {
            options.tickFormatter = dom.findFunction(options.tickFormatter);
        }
        return points.length === 0 ? options : {
            ...options,
            ticks: getPointValues(points, labels),
        };
    };

    const getAxisLabels = ({ ticks: { labels = {} } = {} }, options = {}) => {
        if(types.isString(options.tickFormatter))
        {
            options.tickFormatter = dom.findFunction(options.tickFormatter);
        }
        return points.length === 0 ? options : {
            ...options,
            ticks: getPointValues(points, labels),
        };
    };

    const makeGraph = ({ series: { points, values }, options = {} }) => ({
        ...options,
        data: getPointValues(points, values, options),
    });

    const getGraphConfig = (type, graphs, options, xaxes, yaxes) => {
        if (!types.isArray(graphs) || graphs.length === 0) {
            console.error(`Charts plugin (ChartJs): no valid data to show in the chart with id ${selector}.`);
            return null;
        }

        const data = {
            datasets: graphs.map(graph => makeGraph(graph)),
        };
        // const labels = graphs.map(({ label }) => label);
        // if(types.isArray(xaxes) && xaxes.length > 0)
        // {
        //     data.labels = labels;
        // }

        return { type, data, options };
    };

    const getPieConfig = (type, pie, options) => {
        if (!types.isObject(pie)) {
            console.error(`Charts plugin (ChartJs): no valid data to show in the chart with id ${selector}.`);
            return null;
        }

        const { series, labels, options: opts = {} } = pie;
        const data = {
            datasets: [{
                ...opts,
                data: series.map(({ value }) => value),
            }],
        };
        // Get the labels from the first dataset.
        if(types.isArray(labels))
        {
            data.labels = labels;
        }

        return { type, data, options };
    };

    const drawChart = (wrapper, options) => {
        if (options.type === 'pie') new Chart(wrapper, options);
    };
    const drawChart0 = (wrapper, options) => {
        const DATA_COUNT = 5;
        const NUMBER_CFG = {count: DATA_COUNT, min: 0, max: 100};

        const data = {
            labels: ['Red', 'Orange', 'Yellow', 'Green', 'Blue'],
            datasets: [
                {
                    label: 'Dataset 1',
                    data: Utils.numbers(NUMBER_CFG),
                    backgroundColor: Object.values(Utils.CHART_COLORS),
                }
            ]
        };
        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Chart.js Pie Chart'
                    }
                }
            },
        };
        new Chart(wrapper, config);
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
        const container = document.getElementById(selector);
        if(!container)
        {
            console.error(`Charts plugin (ChartJs): unable to find the DOM element with id ${selector}.`);
            return true;
        }

        // A wrapper is added so the chart destruction can be detected using its removal.
        const wrapperId = `${selector}-wrapper`;
        container.innerHTML = wrapperHtml(container, wrapperId, size);
        const wrapper = document.getElementById(wrapperId);

        const config = type === 'pie' || type === 'donut' ?
            getPieConfig(type, pie, options) :
            getGraphConfig(type, graphs, options, xaxes, yaxes);
        console.log('ChartJs chart config: ' + JSON.stringify({ config }));
        if (config !== null) {
            drawChart(wrapper, config);
        }

        return true;
    };
}));
