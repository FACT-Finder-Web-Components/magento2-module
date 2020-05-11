define([], function () {
    'use strict';

    return function (options, element) {
        if (!options.targetUrl || window.location.href.indexOf(options.targetUrl) === 0) return;

        element.addEventListener('before-search', function (event) {
            if (['productDetail', 'getRecords'].lastIndexOf(event.detail.type) === -1) {
                event.preventDefault();
                delete event.detail.type;
                window.location = options.targetUrl + factfinder.common.dictToParameterString(event.detail);
            }
        });
    }
});
