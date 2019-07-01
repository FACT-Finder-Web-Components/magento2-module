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

    $(document).on('ajax:addToCart', function (e) {
        scrollTo(0, 0);
    });

    document.body.addEventListener('click', function ({target}) {
        if (target.classList.contains('swatch-option')) {
            let optionId = target.getAttribute('option-id'),
                attributeId = target.getAttribute('attribute-id'),
                attributeSelector = target.getAttribute('product-id') + '-' + attributeId,
                notSelected;

            target.classList.add('selected');
            document.getElementById(attributeSelector).value = optionId;
            notSelected = [...document.querySelectorAll('.swatch-option')].filter(function (el) {
                return el.getAttribute('option-id') !== optionId && el.getAttribute('attribute-id') === attributeId;
            });

            notSelected.forEach(function (el) {
                el.classList.remove('selected');
            })
        }
    }, false);
});
