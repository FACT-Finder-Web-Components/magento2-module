define(['factfinder', 'mage/url'], function (factfinder, url) {
    var redirectPath = 'FACT-Finder/result';

    function isSearchResultPage() {
        return window.location.href.indexOf(redirectPath) >= 0;
    }

    document.addEventListener('before-search', function (event) {
        if (!isSearchResultPage() && ['productDetail', 'getRecords'].lastIndexOf(event.detail.type) === -1) {
            event.preventDefault();
            var params = factfinder.common.dictToParameterString(event.detail);
            if (!url.build('')) url.setBaseUrl(BASE_URL || '');
            window.location = url.build(redirectPath + params);
        }
        return event;
    });
});
