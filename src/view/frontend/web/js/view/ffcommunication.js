define(['Magento_Customer/js/customer-data'], function (customerData) {
    'use strict';

    return function (config, element) {
        var sessionData = customerData.get('ffcommunication');
        sessionData.subscribe(function (data) {
            if (!data.uid) return;
            element.sid = data.sid;
            element.userId = data.uid;
        });
        sessionData.valueHasMutated();
    };
});
