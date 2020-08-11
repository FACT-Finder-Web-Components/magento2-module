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
    }
};
