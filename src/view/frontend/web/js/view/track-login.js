define(['Magento_Customer/js/customer-data', 'factfinder', 'mage/cookies'], function (customerData, factfinder) {
    'use strict';

    return function (config, element) {
        customerData.get('login-tracking').subscribe(function (data) {
            const hasJustLoggedIn = jQuery.mage.cookies.get('logged_in');
            if (hasJustLoggedIn) {
                console.warn('Logged in!');
                jQuery.mage.cookies.clear('logged_in');
            }
        });
    };
});
