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
            customerData.reload(['ffcommunication']).done(function (result) {
                var communication = $('ff-communication'),
                    uid = result.ffcommunication.uid,
                    sid = result.ffcommunication.sid;

                communication.attr('sid', sid);
                if (!!uid) {
                    communication.attr('uid', uid);
                }
            });
        }
    });
});
