define([
    'factfinder',
    'jquery',
    'Omikron_Factfinder/js/swatch-renderer',
    'catalogAddToCart'
], function (factfinder, $, swatchRenderer) {
    factfinder.communication.ResultDispatcher.subscribe('records', function (records) {
        records.forEach(function ({record}) {record.Swatches = swatchRenderer(record);});
    });

    document.body.addEventListener('submit', function (event) {
        event.preventDefault();
        if (event.target.classList.contains('product_addtocart_form')) {
            $(event.target).catalogAddToCart().trigger('submit');
        }
    }, false);

    // scroll to top after adding to cart
    $(document).on('ajax:addToCart', function (e) {
        scrollTo(0, 0);
    });
});
