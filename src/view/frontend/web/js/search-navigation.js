define(['factfinder', 'mage/url', 'matchMedia', 'jquery'], function (factfinder, url, matchMedia, $) {
    var redirectPath = 'FACT-Finder/result';

    factfinder.communication.FFCommunicationEventAggregator.addBeforeDispatchingCallback(function (event) {
        if ((event.type === 'search' || event.type === 'navigation-search') && !isSearchResultPage()) {
            var params = factfinder.common.dictToParameterString(event);
            window.location = url.build(redirectPath + params);
        }
        hideMenu();
    });

    factfinder.communication.ResultDispatcher.subscribe('navigation', function (navData) {
        navData.forEach(function (navSection) {
            navSection.forEach(function (navEl) {
                var queryString = navEl.__TARGET_URL__.url.split('?')[1] || '';
                navEl.__TARGET_URL__.setUrl(BASE_URL + redirectPath + '?' + queryString);
            });
        });
        applyMediaMatch();
    });

    function isSearchResultPage() {
        return window.location.href.indexOf(redirectPath) >= 0;
    }

    function applyMediaMatch() {
        matchMedia({
            media: '(min-width: 768px)',
            navigation: document.querySelector('ff-navigation'),
            entry: function () {
                this.navigation.flyout = 'true';
                this.navigation.layout = 'horizontal';
            },
            exit: function () {
                this.navigation.flyout = 'false';
                this.navigation.layout = 'vertical';
            }
        });
    }

    function hideMenu() {
        $('html').removeClass('nav-open nav-before-open');
    }

    window.clickNavigationLink = function (e) {
        if (document.querySelector('ff-navigation').flyout === 'false' || isSearchResultPage()) {
            e.preventDefault();
        }
    }
});
