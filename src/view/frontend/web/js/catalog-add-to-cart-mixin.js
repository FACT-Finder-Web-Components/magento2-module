define([
    'jquery',
    'factfinder',
    'Magento_Customer/js/customer-data'
], function ($, factfinder, customerData) {
    'use strict';
    return function (widget) {
        function unpackOrUndefined(arrayElement) {
            if (Array.isArray(arrayElement) && arrayElement.length) {
                return arrayElement[0];
            }
            return undefined;
        }

        jQuery(document).on('ajax:addToCart', function (_, eventData) {
            const cart = customerData.get('cart');
            let payload, subscription;
            payload = eventData

            if (!subscription || subscription.isDisposed) {
                subscription = cart.subscribe(function (cartData) {
                    if (payload.productInfo && payload.productInfo.length) {
                        const addedProductId = payload.productInfo[0].id;
                        const cartItem = unpackOrUndefined(cartData.items.filter(function (item) {
                            return item.product_id === addedProductId;
                        }));
                        const qtyInput = unpackOrUndefined(jQuery(payload.form[0]).serializeArray().filter(function (element) {
                            return element.name === 'qty';
                        }));

                        if (qtyInput && cartItem) {
                            const track = new factfinder.communication.Tracking12();
                            track.cart({
                                channel: factfinder.communication.globalSearchParameter.channel,
                                sid: factfinder.common.localStorage.getItem('ff_sid'),
                                id: cartItem.product_sku,
                                price: cartItem.product_price_value,
                                masterId: payload.sku,
                                count: parseInt(qtyInput.value)
                            });
                        }
                    }
                    subscription.dispose();
                });
            }
        });
        return widget;
    }
});
