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
                let uid = result.ffcommunication.attributes.uid,
                    sid = result.ffcommunication.attributes.sid;

                $('ff-communication').attr('sid', sid);
                if (uid !== null) {
                    $('ff-communication').attr('uid', uid);
                }
            });
        },
    });
});
