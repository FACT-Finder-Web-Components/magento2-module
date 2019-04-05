var config = {
    map: {
        '*': {
            factfinder: 'Omikron_Factfinder/ff-web-components/bundle.min'
        }
    },
    shim: {
        'Omikron_Factfinder/ff-web-components/bundle.min': {
            deps: [
                'Omikron_Factfinder/ff-web-components/vendor/custom-elements-es5-adapter.min',
                'Omikron_Factfinder/ff-web-components/vendor/webcomponents-loader.min'
            ],
            exports: 'factfinder'
        }
    },
    deps: ['Omikron_Factfinder/js/search-navigation']
};
