/**
 * Class: jaxon.chart
 *
 * global: jaxon
 */

// Register the Chart plugin command.
jaxon.chart = {};
jaxon.dom.ready(() => {
    jaxon.register("charts.card", ({ lib, card }) => jaxon.chart.show(lib, card));
});

(function(self, dom, call, query, log) {
    /**
     * Chart libraries.
     *
     * @var {object}
     */
    const libs = {};

    /**
     * Find a library to execute a given function.
     *
     * @param {string} sLibName The chart library name
     *
     * @returns {object|null}
     */
    const getLib = (sLibName) => {
        if (!libs[sLibName]) {
            log.error(`Unable to find a chart library with name "${sLibName}".`);
            return null;
        }

        return libs[sLibName];
    };

    /**
     * Show a chart card.
     *
     * @param {string} sLibName The chart library to use
     * @param {object} xCard The card content
     *
     * @returns {true} The operation completed successfully.
     */
    self.show = (sLibName, xCard) => {
        getLib(sLibName)?.show(xCard);
        return true;
    };

    /**
     * Register a chart library.
     *
     * @param {string} sLibName The library name
     * @param {callback} xCallback The library definition callback
     *
     * @returns {void}
     */
    self.register = (sLibName, xCallback) => {
        // Create an object for the library
        libs[sLibName] = {};
        // Provide some utility functions to the chart library.
        const utils = {
            ready: dom.ready,
            js: call.execExpr,
            jq: query.select,
        };
        xCallback(libs[sLibName], utils);
    };
})(jaxon.chart, jaxon.dom, jaxon.parser.call, jaxon.parser.query, jaxon.utils.log);
