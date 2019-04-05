define(['factfinder', 'mage/url', 'matchMedia', 'jquery'], function (factfinder, url, matchMedia, $) {
    var redirectPath = 'FACT-Finder/result',
        html = $('html');

    factfinder.communication.FFCommunicationEventAggregator.addBeforeDispatchingCallback(function (event) {
        if ((event.type === 'search' || event.type === 'navigation-search') && !isSearchResultPage()) {
            var params = factfinder.common.dictToParameterString(event);
            window.location = url.build(redirectPath + params);
        }
        hideMenu();
    });

    document.addEventListener('ffReady', function () {
        factfinder.communication.ResultDispatcher.subscribe('navigation', function (navData, e) {
            navData.forEach(function (navSection) {
                navSection.forEach(function (navEl) {
                    var url = navEl.__TARGET_URL__.url.split('?');
                    url = BASE_URL + 'FACT-Finder/result?' + (url[1] ? url[1] : '');
                    navEl.__TARGET_URL__.setUrl(url);
                });
            });
            applyMediaMatch();
        });
    });

    function isSearchResultPage() {
        return window.location.href.indexOf(redirectPath) > 0;
    }

    function applyMediaMatch() {
        matchMedia({
            media: '(min-width: 768px)',
            navigation: jQuery("ff-navigation"),
            entry: function () {
                this.navigation.attr("layout", "horizontal");
                this.navigation.attr("flyout", "true");
            },
            exit: function () {
                this.navigation.attr("layout", "vertical");
                this.navigation.attr("flyout", "false");
            }
        });
    }

    function hideMenu() {
        html.removeClass('nav-open');
        html.removeClass('nav-before-open');
    }
});

function clickNavigationLink(e) {
    if (document.querySelector('ff-navigation').getAttribute('flyout') == 'false') {
        e.preventDefault();
    }
}
