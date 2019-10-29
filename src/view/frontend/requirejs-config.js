var config = {
    map: {
        '*': {
            es6shim: 'Omikron_Factfinder/js/polyfill/es6-shim.min',
            factfinder: 'Omikron_Factfinder/ff-web-components/bundle'
        }
    },
    shim: {
        'Omikron_Factfinder/ff-web-components/bundle': {
            deps: [
                'Omikron_Factfinder/ff-web-components/vendor/custom-elements-es5-adapter',
                'Omikron_Factfinder/ff-web-components/vendor/webcomponents-loader'
            ].concat(!!window.navigator.userAgent.match(/(MSIE |Trident\/)/) ? ['es6shim'] : []),
            exports: 'factfinder'
        }
    },
    deps: ['Omikron_Factfinder/js/search-navigation']
};
