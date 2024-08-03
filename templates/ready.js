/*
 * Jaxon Flot plugin
 */
    $("#flot-tooltip").remove();
    $('<div id="flot-tooltip"></div>').css({
        position: "absolute",
        display: "none",
        border: "1px solid #fdd",
        padding: "2px",
        "background-color": "#fee",
        opacity: 0.80
    }).appendTo("body");

    jaxon.register("flot.plot", function({ plot: { selector, graphs, size, xaxis, yaxis, options = {} } }) {
        let showLabels = false;
        const labels = {};
        const dom = jaxon.utils.dom;
        // Set container size
        if(size.width !== "")
        {
            $(selector).css({width: size.width});
        }
        if(size.height !== "")
        {
            $(selector).css({height: size.height});
        }

        const _graphs = graphs.map(g => {
            const graph = g.options || {};
            graph.data = [];
            if(g.values.data !== null)
            {
                graph.data = g.points.map(x => [x, g.values.data[x]]);
            }
            else if(g.values.func !== null)
            {
                g.values.func = dom.findFunction(g.values.func);
                graph.data = g.points.map(x => [x, g.values.func(x)]);
            }
            if(g.labels.func !== null)
            {
                g.labels.func = dom.findFunction(g.labels.func);
            }
            if(typeof g.options.label !== "undefined" &&
                (g.labels.data !== null || g.labels.func !== null))
            {
                showLabels = true;
                labels[g.options.label] = g.labels;
            }
            return graph;
        });

        // Setting ticks
        if(xaxis.points.length > 0)
        {
            let ticks = [];
            if(xaxis.labels.data !== null)
            {
                ticks = xaxis.points.map(point => [point, xaxis.labels.data[point]]);
            }
            else if(xaxis.labels.func !== null)
            {
                xaxis.labels.func = dom.findFunction(xaxis.labels.func);
                ticks = xaxis.points.map(point => [point, xaxis.labels.func(point)]);
            }
            if(ticks.length > 0)
            {
                options.xaxis = {ticks: ticks};
            }
        }
        /*if(yaxis.points.length > 0)
        {
        }*/

        if(showLabels)
        {
            options.grid = {hoverable: true};
        }
        $.plot(selector, _graphs, options);

        // Labels
        if(showLabels)
        {
            $(selector).bind("plothover", function (event, pos, item) {
                if(!item)
                {
                    $("#flot-tooltip").hide();
                    return;
                }
                const series = item.series.label;
                const x = item.datapoint[0]; // item.datapoint[0].toFixed(2);
                const y = item.datapoint[1]; // item.datapoint[1].toFixed(2);
                let tooltip = "";
                if(typeof labels[series] !== "undefined")
                {
                    const _labels = labels[series];
                    if(_labels.data != null && typeof _labels.data[x] !== "undefined")
                    {
                        tooltip = _labels.data[x];
                    }
                    else if(_labels.func != null)
                    {
                        tooltip = _labels.func(series, x, y);
                    }
                }
                if((tooltip))
                {
                    $("#flot-tooltip").html(tooltip).css({top: item.pageY+5, left: item.pageX+5}).fadeIn(200);
                }
            });
        }
        return true;
    });
