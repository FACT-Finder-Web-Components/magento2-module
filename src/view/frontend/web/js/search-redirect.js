define(['factfinder'], function (factfinder) {
    'use strict';

    return function (options, element) {
        if (!options.targetUrl || window.location.href.indexOf(options.targetUrl) === 0) return;

        element.addEventListener('before-search', function (event) {
            if (['productDetail', 'getRecords'].lastIndexOf(event.detail.type) === -1) {
                event.preventDefault();
                window.location = options.targetUrl + factfinder.common.dictToParameterString(factfinder.common.encodeDict(event.detail));
            }
        });

        if (!window.hasOwnProperty('ffRedirectToSearchResultPage')) {
            window.ffRedirectToSearchResultPage = function (query, addlParams) {
                const detail = Object.assign({type: 'search', query: query}, addlParams);
                const event = new CustomEvent('before-search', {
                    detail: detail,
                    bubbles: true,
                    cancelable: true,
                    composed: true
                });
                element.dispatchEvent(event);
            }
        }
    }
});
