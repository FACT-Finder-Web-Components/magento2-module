define(function () {
    return {
        scrollToCallback: function (selector, config) {
            return function () {
                const defaultConfig = {
                    behavior: 'smooth',
                    top: 0,
                    left: 0
                };
                const mergedConfig = Object.assign({}, defaultConfig, config);

                if (selector) {
                    const element = document.querySelector(selector);
                    !element ? console.error('Invalid selector', selector) : element.scrollIntoView(mergedConfig);
                } else {
                    window.scrollTo(mergedConfig);
                }
            }
        }
    }
});
