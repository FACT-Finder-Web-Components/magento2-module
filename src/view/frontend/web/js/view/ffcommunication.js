define(['Magento_Customer/js/customer-data', 'factfinder'], function (customerData, factfinder) {
    'use strict';

    return function (config, element) {
        var sessionData = customerData.get('ffcommunication');
        sessionData.subscribe(function (data) {
            if (!data.uid) {
                const uidKey = Object.keys(localStorage).find(function (key) {
                    return key.indexOf(factfinder.common.localStorage.getItem('ff_sid')) === 0
                });
                if (uidKey) factfinder.common.localStorage.setItem(uidKey, null);
            }

            const storedUserId = factfinder.common.localStorage.getItem(factfinder.common.localStorage.getItem('ff_sid') + ':' + data.uid);
            if (data.uid && data.uid !== element.userId && (!storedUserId || storedUserId !== data.uid.toString())) {
                element.userId = data.uid;
            }

            if (data.internal) element.addParams = (element.addParams ? element.addParams + ',' : '') + 'log=internal';
        });
        sessionData.valueHasMutated();
    };
});
