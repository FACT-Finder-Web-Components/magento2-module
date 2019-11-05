define(['Magento_Customer/js/customer-data'], function (customerData) {
    'use strict';

    return function (config, element) {
        var sessionData = customerData.get('ffcommunication');
        sessionData.subscribe(function (data) {
            if (!data.uid) return;
            element.userId = data.uid;
            element.sid = data.sid;
        });
        sessionData.valueHasMutated();
    };
});
