define([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data'
], function ($, _, customerData) {
    'use strict';

    $(document).on('ajax:addToCart', function (event, eventData) {

        if (typeof window.factfinder === 'undefined') {
            return;
        }

        require(['factfinder'], function (factfinder) {
            const cart = customerData.get('cart');
            const productId = _.first(eventData.productIds);
            let subscription;

            if (productId && (!subscription || subscription.isDisposed)) {
                subscription = cart.subscribe(function (cartData) {
                    const cartItem = _.find(cartData.items, function (item) {
                        return item.product_id === productId;
                    });

                    const qtyInput = _.find($(eventData.form[0]).serializeArray(), function (element) {
                        return element.name === 'qty';
                    });

                    if (qtyInput && cartItem) {
                        let sid = '';
                        try {
                            sid = JSON.parse(localStorage.getItem('ffwebco') || '{}').sid || '';
                        } catch (e) {
                            console.warn('FactFinder: Nie można pobrać SID z localStorage');
                        }

                        const track = factfinder.tracking;
                        track.cart([{
                            id: cartItem.product_sku,
                            price: parseFloat(cartItem.product_price_value),
                            masterId: eventData.sku || cartItem.product_sku,
                            count: parseInt(qtyInput.value),
                            sid: sid
                        }]);
                    }

                    subscription.dispose();
                });
            }
        });
    });

    return _.identity;
});
