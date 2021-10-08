define([
    'jquery',
    'factfinder',
    'underscore',
    'Magento_Customer/js/customer-data'
], function ($, factfinder, _, customerData) {
    'use strict';

    $(document).on('ajax:addToCart', function (event, eventData) {
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
                    const track = new factfinder.communication.Tracking12();
                    track.cart({
                        channel: factfinder.communication.globalSearchParameter.channel,
                        id: cartItem.product_sku,
                        price: cartItem.product_price_value,
                        masterId: eventData.sku || cartItem.product_sku,
                        count: parseInt(qtyInput.value)
                    });
                }

                subscription.dispose();
            });
        }
    });

    return _.identity;
});
