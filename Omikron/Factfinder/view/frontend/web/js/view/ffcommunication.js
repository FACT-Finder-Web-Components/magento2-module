define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery'
], function (Component, customerData, $) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            let getRoles = this.getFieldRoles.bind(this);

            customerData.reload(['ffcommunication']).done(function (result) {
                $('body').prepend(result.ffcommunication.component);
            });

            document.addEventListener("ffReady", function () {
                factfinder.communication.fieldRoles = getRoles();
            });
        },

        /**
         * @return JSON
         */
        getFieldRoles: function () {
            return this.fieldRoles;
        },
    });
});
