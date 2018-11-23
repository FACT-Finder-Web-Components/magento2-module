document.addEventListener("ffReady", function () {
    factfinder.communication.FFCommunicationEventAggregator.addBeforeDispatchingCallback(function (event) {
        var urlArr = window.location.href.split("/");
        var domainUrl = urlArr[0] + "//" + urlArr[2];
        var redirectPath = '/FACT-Finder/result/';

        if (event.type === "search" && window.location.href.match(/FACT-Finder\/result/) === null) {
            preventNativeSearch(event);
            var params = factfinder.common.dictToParameterString(event);
            window.location.href = domainUrl + redirectPath + params;
            delete event.type;
        }
    });

    function preventNativeSearch(event) {
        delete event.type;
        delete event.version;
    }
});
