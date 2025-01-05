/**
 * Class: jaxon.dialog.lib.tingle
 */

jaxon.dialog.lib.register('tingle', (self, { js, types }) => {
    /**
     * @var {object}
     */
    const dialog = {
        dom: null,
    };

    /**
     * Show the modal dialog
     *
     * @param {string} title The dialog title
     * @param {string} content The dialog HTML content
     * @param {array} buttons The dialog buttons
     * @param {array} options The dialog options
     * @param {function} jsElement A callback to call with the dialog js content element
     *
     * @returns {object}
     */
    self.show = (title, content, buttons, options, jsElement) => {
        self.hide();
        dialog.dom = new tingle.modal({
            footer: true,
            stickyFooter: false,
            closeMethods: ['overlay', 'button', 'escape'],
            ...options,
        });
        // Set content
        dialog.dom.setContent('<h2>' + title + '</h2>' + content);
        // Add buttons
        buttons.forEach(({ title, class: btnClass, click }) => {
            const handler = types.isObject(click) ?
                () => js.execExpr(click) : () => self.hide();
            dialog.dom.addFooterBtn(title, btnClass, handler);
        });
        // Open the modal
        dialog.dom.open();
        // Pass the js content element to the callback.
        jsElement(dialog.dom.modalBox);
    };

    /**
     * Hide the modal dialog
     *
     * @returns {void}
     */
    self.hide = () => {
        if(!dialog.dom)
        {
            return;
        }
        // Close an destroy the modal
        dialog.dom.close();
        dialog.dom.destroy();
        dialog.dom = null;
    };
});
