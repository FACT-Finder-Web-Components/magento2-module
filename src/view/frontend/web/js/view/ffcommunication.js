define(['Magento_Customer/js/customer-data', 'factfinder','underscore',], function (customerData, factfinder, _) {
    'use strict';

    return function (config, element) {
        var sessionData = customerData.get('ffcommunication');
        sessionData.subscribe(function (data) {
            if (!data.uid) {
                const uidKey = _.find(Object.keys(localStorage), function (key) {
                    return key.indexOf(factfinder.common.localStorage.getItem('ff_sid')) === 0
                });
                if (uidKey) factfinder.common.localStorage.setItem(uidKey, null);
            }

            if (data.uid) {
                element.setAttribute('user-id', data.uid);
            }

            if (data.internal) {
                element.setAttribute('add-params', (element.addParams ? element.addParams + ',' : '') + 'log=internal');
            }
        });
        sessionData.valueHasMutated();
    };
});



