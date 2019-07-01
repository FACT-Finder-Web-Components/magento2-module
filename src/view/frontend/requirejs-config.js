var config = {
    map: {
        '*': {
            factfinder: 'Omikron_Factfinder/ff-web-components/bundle'
        }
    },
    shim: {
        'Omikron_Factfinder/ff-web-components/bundle': {
            deps: [
                'Omikron_Factfinder/ff-web-components/vendor/custom-elements-es5-adapter',
                'Omikron_Factfinder/ff-web-components/vendor/webcomponents-loader'
            ],
            exports: 'factfinder'
        }
    },
    deps: ['Omikron_Factfinder/js/search-navigation','Omikron_Factfinder/js/catalog-add-to-cart', 'Omikron_Factfinder/js/swatch-renderer']
};
