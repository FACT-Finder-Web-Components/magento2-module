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
            var cacheKey               = 'ffcommunication',
                communicationData      = customerData.get('ffcommunication')(),
                communicationComponent = $('ff-communication');

            if (!communicationData.loggedIn && !communicationComponent.attr('user-id')) {
                customerData.reload([cacheKey]).done(function (result) {
                    var communicationComponent = $('ff-communication'),
                        uid = result.ffcommunication.uid,
                        sid = result.ffcommunication.sid;

                    communicationComponent.attr('sid', sid);
                    if (!!uid) {
                        communicationComponent.attr('user-id', uid);
                        customerData.set(cacheKey, Object.assign({loggedIn: true}, result.ffcommunication));
                    }
                });
            }
        }
    });
});
