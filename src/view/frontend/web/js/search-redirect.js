define(['factfinder', 'mage/url'], function (factfinder, url) {
    factfinder.communication.FFCommunicationEventAggregator.addBeforeDispatchingCallback(function (event) {
        var redirectPath = 'FACT-Finder/result';
        if ((event.type === 'search' || event.type === 'navigation-search') && window.location.href.indexOf(redirectPath) < 0) {
            var params = factfinder.common.dictToParameterString(event);
            window.location = url.build(redirectPath + params);
        }
    });

    document.addEventListener(`ffReady`, () => {
        factfinder.communication.ResultDispatcher.subscribe(`navigation`, (navData, e) => {
            if (window.location.href.match(/FACT-Finder\/result/) === null) {
                navData.forEach(navSection => navSection.forEach(navEl => {
                        let url = navEl.__TARGET_URL__.url.split('?');
                        url.splice(1, 0, 'FACT-Finder/result?');
                        navEl.__TARGET_URL__.setUrl(url.join(''));
                    }
                ))
            }
        });
    });

    window.addEventListener("resize", function (evt) {
        let navigation = document.querySelector("ff-navigation");
        if (window.innerWidth < 768) {
            navigation.setAttribute("layout", "vertical");
            navigation.setAttribute("flyout", "false");
        } else {
            navigation.setAttribute("layout", "horizontal");
            navigation.setAttribute("flyout", "true");
        }
    });
});
