define(['Magento_Customer/js/customer-data'], function (customerData) {
    'use strict';

    return function (config, element) {
        var sessionData = customerData.get('ffcommunication');
        sessionData.subscribe(function (data) {
            if (data.uid && data.uid !== element.userId) element.userId = data.uid;
            if (data.internal) element.addParams = (element.addParams ? element.addParams + ',' : '') + 'log=internal';
        });
        sessionData.valueHasMutated();
    };
});
