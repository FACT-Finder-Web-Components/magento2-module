define(['factfinder', 'mage/url'], function (factfinder, url) {
    var redirectPath = 'FACT-Finder/result';

    factfinder.communication.EventAggregator.addBeforeDispatchingCallback(function (event) {
        if (event.type === 'search' && !isSearchResultPage() && !event.__immediate) {
            delete event.type;
            var params = factfinder.common.dictToParameterString(event);
            if (!url.build('')) url.setBaseUrl(BASE_URL || '');
            factfinder.common.localStorage.setItem('ff_no_redirect');
            window.location = url.build(redirectPath + params);
        }
    });

    function isSearchResultPage() {
        return window.location.href.indexOf(redirectPath) >= 0;
    }
});
