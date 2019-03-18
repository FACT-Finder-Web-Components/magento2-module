define(['factfinder', 'mage/url'], function (factfinder, url) {
    factfinder.communication.FFCommunicationEventAggregator.addBeforeDispatchingCallback(function (event) {
        var redirectPath = 'FACT-Finder/result';
        if (event.type === 'search' && window.location.href.indexOf(redirectPath) < 0) {
            var params = factfinder.common.dictToParameterString(event);
            window.location = url.build(redirectPath + params);
        }
    });
});
