# 1.2.14
## FIX
- up/down arrow navigation fix for ff-onfocus-suggest and ff-suggest component
- fixed erroneous filtering when filter name contains unrelated substring "sort"
- infinite scrolling of `ff-record-list` doesn't work reliably due to race condition on page load
- fixed rendering of ff-slider buttons when detached and attached to dom

## CHANGE
- new `--nav-element-a` mixin to allow anchor elements in `ff-header-navigation` to be styled even in shadow DOM


#1.2.13
## ADD
- new element `ff-sortbox-select`
- new element `ff-products-per-page-select`
- new elements `ff-middleware` and `ff-multi-attribute-parsing`. These can be used to configure how multi-attribute fields in the FACT-Finder response shall be parsed in order to access their values more easily. Alternatively, the same configuration is also possible with plain JavaScript. See https://web-components.fact-finder.de/api/ff-middleware#tab=docs for details
- for a better debugging experience `ff-header-navigation` logs now when it hides 2nd layer navigation elements without 3rd layer children. If you want to show 2nd layer navigation without 3rd layer children, you can set the attribute `hide-empty-groups="false"`

## FIX
- `ff-asn-group-slider` does not hide anymore when its `absoluteMinValue` is `0`
- CTRL + click now opens links from elements with `data-redirect` (click tracking) attributes in a new tab
- clicking history back redirects to previous page now when immediate search is on, there is no need to click twice
- in addition to left mouse clicks also CTRL+LMB, SHIFT+LMB and MMB are now tracked on elements with `data-redirect` attribute

## CHANGE
- `query` parameter is no longer included in navigation requests

# 1.2.12
## BREAKING
- new `<a>` elements in `ff-header-navigation` are likely to break styling
- the `navigation` event emitted by `factfinder.communication.ResultDispatcher` now passes an array of navigation elements grouped by `clusterLevel` instead of a flat array of the first level of elements

## ADD
- `ff-header-navigation` now renders `<a>` elements to allow right-click navigation. `href`'s are customizable. Also see breaking changes!
- log warning about improperly used Boolean properties
- `ff-campaign-redirect` now has a Boolean attribute `relative-to-origin` to optionally enable FACT-Finder redirect campaigns to be configured with relative destination urls

## FIX
- the `count` parameter in tracking requests now always defaults to 1 when not provided explicitly
- `ff-campaign-redirect` now immediately redirects preventing rendering of content
- do never provide master ID instead of tracking ID in recommendation click tracking
- fix `ff-onfocus-suggest` component, which wasn't shown due to an internal error
- `ff-campaign-shopping-cart` now respects comma separated values

## CHANGE
- navigation elements passed by the `navigation` event emitted by `factfinder.communication.ResultDispatcher` now have a `__SUB_ELEMENTS__` property containing all immediate child elements
- `ff-communication` registers with the `WebComponentsReady` event to call `factfinder.communication.ResultDispatcher.startDispatching()`. This is necessary due to a change in ff-core which now prevents search responses from going unnoticed by Web Components that are initialized too late. `ff-similar-products` and `ff-recommendation` combatted this behaviour by sending an extra request, which they no longer do
- `ff-communication` search-immediate does now invoke the search much earlier than before. Dispatching occurs on `WebComponentsReady` event.

1.2.11
FIX
fixed navigation tracking when using use-url-params="false"

1.2.10
ADD
log information about missing fieldRoles when localizing currencies
log information about missing configuration
log information about missing fieldRoles on tracking requests

FIX
fixed a tracking bug where origPageSize could be calculate wrong

CHG
data-redirect does now use an imperative event listener to prevent accidental overrides to onclick which could cause tracking request to be aborted before tracking succeeds

Meaningful notes:
we removed a behavior from the tracking class, where sending a tracking request was setting the global channel for all elements

1.2.9
ADD
boolean attribute "disable-auto-expand" in ff-asn-group and ff-asn-group-slider which will prevent asn groups with active filters from expanding automatically
FIX
ff-communication currency-min/max-digits functionality restored
fixed a bug where campaigns were disappearing after changing page or sort
fixed a bug where currency symbols were not appearing for shopping cart campaign pushed products

1.2.8
fixed an error related to recommendationClick tracking on product detail pages.
fixed a rare bug where similar products and recommendations are not displayed on first request
ensure redirect happens if tracking request fails

1.2.7
See https://github.com/FACT-Finder-Web-Components/ff-web-components/releases/tag/1.2.7

1.2.7-pre-release-23
FIX
Fixed a bug where currencies were not saved to history and therefore were lost
If the JSON response is too big for the browser history a mock object is pushed to the history which is used to send a new request to FACT-Finder in order to retrieve the old response.

1.2.7-pre-release-22
CHG
ff-slider wrapper div#sliderX got a min-width: 1px in order to prevent slider which occur if the wrapper or the [data-slider] element have a width of 0px
FIX
ff-slider-control input are not updating the slider position

1.2.7-pre-release-21
ADD
string attribute <ff-communication sort-url-parameters-alphabetically="true"> which will cause all http parameters and the related parameter values to be alphabetically sorted
boolean attribute <ff-communication async-facets> which will cause the facets to be rendered after the records are rendered

1.2.7-pre-release-20
FIX: appending http parameters without a value doesn't cause an error anymore

1.2.7-pre-release-19
CHG: ff-slider element does now encode special characters like ~ or ä in parameter names
FIX: <ff-communication use-url-parameter="false"> does now respect all events. Before it could happen that url parameters were pushed by accident.

1.2.7-pre-release-18
HOTFIX: fixed a regex issue regarding suggest query highlighting introduced in 1.2.7-pre-release-16

1.2.7-pre-release-17
CHG: search requests with custom topics won't update the currentSearchResult property and the url parameters
ADD: new ff-checkout-tracking element

1.2.7-pre-release-16
FIX: wrong unit when using <ff-communication currency-code=""> for slider-control inputs
FIX: suggest highlights only one word when searching for multiple words
FIX: ff-suggest-item query highlighting does now properly escape special characters
FIX: jumping slider buttons

1.2.7-pre-release-15
FIX: ff-suggest is not rendered when detached and attached again after initialization.
FIX: currency-code="CODE" currency-country-code="CODE" does now respect multi values like: 100.00 - 150.00
FIX: ASN groups wrong order
CHG: ff-slider-control does now update currencies when group changes -> before only on initialization

1.2.7-pre-release-14
FIX: ff-asn-group hiddenLinks are not rendered when detailedLinks is set to 0
FIX: remove ff-slider log
ADD: ff-record/ff-record-list stamp-always attribute
     By default ff-record does not stamp the dom if the old recordData and the new recordData dont differ by id record.id !== record.id
     If you set the Boolean stamp-always attribute the dom is always stamped even if the id's dont differ (<ff-record-list stamp-always>...</...)
     ff-record-list will always set stamp-always on its ff-record elements

1.2.7-pre-release-13
ADD: ff-suggest-item and ff-asn-group-element are now supporting data-image="{{myOwnBindign}}"

1.2.7-pre-release-12
ADD: <option> element does now support data binding when using <ff-asn-group select-box="true">

1.2.7-pre-release-11
CHG: dispatch campaigns on paging events
CHG: do not update records in ff-record-list if the dispatched records are the same as the old records

1.2.7-pre-release-10
ADD: currency-min-digits and currency-max-digits to ff-communication for currency localization configuration

1.2.7-pre-release-9
FIX: currency-code="GBP" and currency-country-code="en-GB" are now working on page 2 and following

1.2.7-pre-release-8
FIX: fixed mustache.js bug when using requirejs introduced with the upgrade to mustache 2.3.ß
FIX: removed a console.log

1.2.7-pre-release-7
ADD: possibility to localize multiple price fields with the "currency-fields" attribute on ff-communication

1.2.7-pre-release-6
ADD: add only-search-params and parameter-whitelist to ff-communication

1.2.7-pre-release-5
CHG: use-url-parameter="false" in conjunction with keep-url-params="all" does now prevent webcomponents from removing custom http parameter

1.2.7-pre-release-4
BUG: fix slider hide bug when min=max value

1.2.7-pre-release-3
CHG: remove infiniteBorder div in ff-record-list when no infinite-scrolling is active
CHG: ff-slider uses now selectedMin/Max as absoluteMin/Max if selected > absolute

1.2.7-pre-release-2
ADD: add category filtering with filterPath and pretty urls
ADD: ff-slider: slider resets now the filter if values are back to absolute min/max
ADD: ff-asn-group gets select-box (<select>) for hiddenLinks as alternative
FIX: topic "result" is now dispatched again on ppp, paging and sort events

1.2.7-pre-release-1
FIX: fix a bug where unit is not shown at first request

Latest 1.2.6
FIX: fix bug in ff-paging-dropdown when dispatching result of new products-per-page
FIX: fix check for deeplink on on-record-redirect
FIX: fix wrong max width calculation for ff-slider
FIX: ff-communication iphone wrong null check when using single-hit-redirect-base-path
fix: fixed a transition bug where a transition was executed with an transition duration of 0s instead of its original transition-duration value

Style Changes
CHG: ff-asn-group-element - in some cases a style="display: inline;" was added to the [data-selected] [data-unselected]
CHG: ff-asn-group-element - we've changed the wrapping divs (#selected, #unselectd) display property to display: inline-block instead of display: block; If you want to revert the changes use the new mixins --unselected-container and --selected-container to set the properties back to block

1.2.5
FIX: ff-onfocus-suggest replace nodelist forEach with normal for-loops
FIX: reset ff-carousel to slide to first slide after the records have changed
CHG: make filtering without clusterLevel possible again
ADD: add single-hit-redirect-base-path to ff-communication for redirect to deeplink when just 1 record is in result
FIX: fixed a rare breadcrumb bug

1.2.4
FIX: fixed slider control bug where undefined was set as a value when no unit was configured
FIX: fixed a bug where the right slider could cross the right border
FIX: error ref_node to insertBefore is not a child of this node

1.2.3
BREAKING CHANGE: we've untied the ff-slider-control templates completely to allow more layout flexibility. This may break you styles because the html order is now kept like supplied.
ADD: usePerso attribute to ff-recommendation
ADD: ff-slider-control input element attributes are now stamped with the template engine like: <input type="text" data-control='1' data-read-only-suffix="{{group.unit}}"/>
FIX: fixed a tracking issue in pushed products campaigns
CHG: rename mixin's for ff-carousel legend to bullets and add record observer
FIX: fixed occasional bug where right slider jumps to the left side and cant be moved afterwards

1.2.2
FIX: ff-asn-group-slider is not opened when group filters are active
ADD: ff-communication does now support 2 new attribute currency-code="GBP" currency-country-code="en-GB" all prices can now automatically be rewritten according to this new settings
ADD: improve validation on slider-control input fields
ADD: add option "all" for keep-url-params which will keep all non FACT-Finder related params
FIX: timing issues on out of order suggest results
ADD: add suggest-delay to ff-searchbox to customize the react time of the input.
FIX: log error if image data is not in record instead of sending a request with /undefiend
ADD: infinite-scroll-margin to ff-record-list
FIX: added new regex for query param

1.2.1
FIX: fixed ff-paging-item changes type attribute to currentLink
FIX: query param was encoded twice in tracking requests
FIX. ff-searchbox value was shown url encoded when loading the page the first time
ADD: add a data-anchor attribute to the ff-record to resolve hyper links
ADD: add 'data-track-count' to TrackingBehavior
FIX: don't dispatch to the breadcrumb after a paging, ppp or sort

1.2.0
ADD: ff-slider-control introduces new mixins
ADD: ff-paging-item does now set the css class disabled when it is not active
FIX: fixed a bug for ff-record data-redirect where some data-redirect targets other than _self could lead to flaky click tracking
FIX: pushed wrong url to browser history when using a html <base> tag
ADD: ff-paging-dropdown element
ADD: Backwards compatibility for FF < 7.3 and "productName" ff-suggest-item click
FIX: broken layout of container in ff-header-navigation
ADD: add "ff-asn-remove-all-filter" element
ADD: suggest-product-record event to ff-suggest which contains the product fetched by the REST call on a suggest productName click
ADD: ff-suggest-item uses the REST get records API to fetch a product on a suggest productName click
ADD: getRecordsUrl property on ff-communication
CHG: core Dispatcher and custom topics
FIX: ignore 'ignorePage' lazy check on searchImmediate
CHG: core Distapcher and custom topics
ADD: infinite scrolling to ff-record-list
CHG: add dynamic slots and change the layout of the container in ff-header-navigation
CHG: don't dispatch asn on a sort, paging or ppp event
ADD: add custom urls to ff-communication
FIX: ASN filter did not consider the ClusterLevel of an element

1.1.10
FIX: Overriding the default action of the ff-suggest-item click

1.1.9
FIX: add fallback for not supported localStorage in Safari inkognito mode

1.1.8
FIX: fix a bug where in Safari 10.1 ff-asn-groups collapse and are not openable

1.1.7
FIX: ff-asn crashs without configured slider

1.1.6
CHG: removed blank between numerical values of slider filter values as the FF-UI diagnostic search can't handle them
CHG: change ff-paging inheritance behavior for showOnly property
FIX: init of ff-asn-group template in ff-asn and asn-group-element template upgrading in ff-asn-group
FIX: ff-asn-group-slider are always shown
CHG: add lazy-rendering for detailedLinks in asn-group
ADD: ff-communication does now send login event when user-id attribute changes. If set, all tracking requests will have the userId as an additional parameter
ADD: ff-communication add sid attribute
CHG: campaign dispatching dose not dispatch 'undefined' to campaigns which are affected.
FIX: set asn-group element 'opened' when group has a selectedElement in searchResult

1.1.5
FIX: shoppingCartCampaign dispatching bug
ADD: ff-search-feedback dont-sho-on-result-changed attribute

1.1.4
FIX: fix suggest 401 bug
FIX: ff-tag-cloud double encoding bug
ADD: ff-suggest-item adds now queryFromSuggest

1.1.3
ADD shopping cart campaign API element (ff-campaign-shopping-cart)
ADD ff-tag-cloud
ADD lazy-load for ff-asn-group
ADD ff-compare element
ADD factfinder.communication.FFCommunicationEventAggregator.addBeforeDispatchingCallback(fn)

1.1.0
ADD: ff-search-feedback element
ADD: ff-campaign-landing-page element
ADD: ff-navigation element
ADD: ff-header-navigation element

1.0.16
fix: console error log on tracking request
fix: recommendation are not displayed

1.0.15
ff-communiction: fixed search-immediate always true when use-seo="true"
ff-slider: fixed encoding issue
ff-breadcrumb-trail-item: does now reflect type attribute
fixed tracking 401 bug

1.0.14
ff-searchbox: fixed on key left right console error when no ff-suggest available
ff-communication: fixed encoding issue when use-seo was set to true
ff-campaign-advisor: fixed polyfill related display bug which caused the advisor question not to show up occasionally in firefox and IE
ff-communication: fixed a bug where setting the version attribute to 7.2 resulted in console log: version ont supported


1.0.12
ff-campaign-advisor bugfix: The ff-campaign-advisor-questions contains now the ff-campaign-advisor-answer as it should be. Therefore the ff-campaign-advisor element is now able to process more than 1 advisor question.
add use-browser-history="true/false" to ff-communication
add use-url-parameter="true/false" to ff-communication
minor improvements

1.0.10 -> 1.0.11
fixed umlaut encoding bug in suggest searchTerms

1.0.9 -> 1.0.10
fixed: search-immediate wasn't occasionally executed in Internet Explorer

1.0.8 -> 1.0.9
ff-asn does now set style="display:none;" at creation time
fixed search-immediate error again

1.0.7 -> 1.0.8
fixed a bug where no tracking request was send on data-redirect
add factfinder.communication.version

1.0.6 -> 1.0.7
fixed onerror-image uncaught exception

1.0.5 -> 1.0.6
Renamed shadow dom css class from container to ff-asn-group-container due to polyfill lack of scoping
Add removeUnresolvedAttribute behavior. It's now possible to prevent FOUC with the [unresovled] attribute on all ff-* elements with visual component
[data-image] attribute is now automatically resolved with the appropriate fieldRole value if possible
ff-sortbox does not change the key anymore when showSelected is true (currentKey + "_showSelected")