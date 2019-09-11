var config = {
    map: {
        '*': {
            factfinder: 'Omikron_Factfinder/ff-web-components/bundle',
            iepolyfills: 'Omikron_Factfinder/js/polyfill/ie11/polyfill'
        }
    },
    shim: {
        'Omikron_Factfinder/ff-web-components/bundle': {
            deps: [
                'Omikron_Factfinder/ff-web-components/vendor/custom-elements-es5-adapter',
                'Omikron_Factfinder/ff-web-components/vendor/webcomponents-loader'
            ],
            exports: 'factfinder'
        },
        'Omikron_Factfinder/js/polyfill/ie11/polyfill': {
            exports: 'iepolyfills'
        }
    },
    deps: ['Omikron_Factfinder/js/search-navigation',]
};
