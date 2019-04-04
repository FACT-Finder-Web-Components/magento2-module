define(['factfinder', 'mage/url', 'matchMedia', 'jquery'], function (factfinder, url, matchMedia, $) {
    var redirectPath = 'FACT-Finder/result';
    factfinder.communication.FFCommunicationEventAggregator.addBeforeDispatchingCallback(function (event) {
        var redirectPath = 'FACT-Finder/result';
        if ((event.type === 'search' || event.type === 'navigation-search') && !isSearchResultPage()) {
            var params = factfinder.common.dictToParameterString(event);
            window.location = url.build(redirectPath + params);
        }
        $('html').removeClass('nav-open');
        $('html').removeClass('nav-before-open');
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

   function preventStandardClick() {
       $('.ff-navigation-link').each(function (index, element) {
           $(element).attr('onclick','event.preventDefault()');
       });
   }

    function allowStandardClick() {
        $('.ff-navigation-link').each(function (index, element) {
            $(element).removeAttr('onclick');
        });
    }

    function isSearchResultPage() {
        return window.location.href.indexOf(redirectPath) > 0;
    }

    function applyMediaMatch() {
        matchMedia({
            media: '(min-width: 768px)',
            navigation: $("ff-navigation"),
            entry: function () {
                this.navigation.attr("layout", "horizontal");
                this.navigation.attr("flyout", "true");
                if (isSearchResultPage()) {
                    preventStandardClick();
                } else {
                    allowStandardClick();
                }
            },
            exit: function () {
                this.navigation.attr("layout", "vertical");
                this.navigation.attr("flyout", "false");
                preventStandardClick();
            }
        });
    }
});
