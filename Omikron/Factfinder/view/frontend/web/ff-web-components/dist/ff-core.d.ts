declare var isDebug: any;
declare var isLog: any;
declare var isProduction: any;
declare module scope.common {
    var logLevel: string;
    module Logger {
        var isDebug: boolean;
        var isLog: boolean;
        var isProduction: boolean;
        function log(message: string, ...args: any[]): void;
        function error(message: string, ...args: any[]): void;
        function debug(message: string, ...args: any[]): void;
        function warn(message: string, ...args: any[]): void;
    }
}
declare module scope {
    var isReady: boolean;
}
declare module scope.common {
    /**
     * If the FF response is too big for the browser history you can use this class a mock.
     * If this class is encountered in popstate the appropriate FF-Request is send again instead of retrieving it from the browser history
     */
    class HistoryEntry {
        static _typeFlag: string;
        searchParams: string;
        /**
         * static _typeFlag isnt serialized in some browser somehow.
         * Maybe related to ts compilation or the way history.push works -> Functions are not passed too, seems like its serialized
         */
        _typeFlag: string;
        constructor(searchParams: any);
    }
    function executeCallback(callback: any): void;
    function elementToString(element: any): string;
    function stringToElement(str: string): any;
    function isArray(obj: any): boolean;
    function transitionEndEventName(): any;
    function setStyleProperty(propertyName: any, propertyValue: any, element: any): void;
    /**
     *
     * @param fn1 this function is called before fn2
     * @param fn2 this function is called after fn1
     * @returns {function(): Function} A function ensuring both methods are called in the parameter order.
     */
    function concatFunctions(fn1: any, fn2: any): () => void;
    /**
     *
     * @param targetElement the element to add the event listener.
     * @param eventName the name of the event. use >= 10 eventnames
     * @param fn callback function
     * @returns {boolean} true if the eventlistener is added otherwise false
     */
    function addEventListener(targetElement: any, eventName: any, fn: any): Boolean;
    function whenBrowserReady(fn: any): void;
    /**
     * @throws Unable to copy object
     * @param obj
     * @returns {any}
     */
    function cloneObject(obj: any): any;
    function copyHTMLAttributes(from: any, to: any): void;
    function isCustomElement(element: any): Boolean;
    function whichTransitionEvent(): any;
    function getParentElementByName(element: any, parentName: any): any;
    /**
     *
     * @param url
     * @returns {{}}
     */
    function urlStringToDict(url?: string): {};
    /**
     * Returns the parameter string of the url param, or if not supplied, the param string of the current browser location.href
     * @param {string} url
     * @returns {string}
     */
    function getParameterString(url?: string): string;
    /**
     * Turns a parameter string into an object.
     * @param parameterString
     * @returns {{}}
     */
    function parameterStringToDict(parameterString: string, decode?: boolean): {};
    function keys(o: any): any[];
    function sortStringArrayAlphabetically(strings: any): any;
    function isFilterParameter(paramName: any): boolean;
    /**
     * Creates a request parameter string of keys and values of the given object. Does not encode
     * @param dict
     * @param sortAlphabetically
     * @returns {string}
     */
    function dictToParameterString(dict: any, sortAlphabetically?: boolean): string;
    function sortFilterValuesAlphabetically(filterValues: any): any;
    /**
     * This will set a url parameter into the current browser url bar.
     * When no value is given, then the parameter will be removed from the url bar.
     *
     * @param name
     * @param sortParameters
     * @param value
     */
    function setUrlParameter(name: string, value?: any, sortParameters?: boolean): void;
    function encodeUrl(url: any, sortParameters?: boolean): string;
    /**
     * Do some encoding for a FF conform URL
     *
     * @info: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent
     *
     * @param str
     * @returns {string}
     */
    function fixedEncodeURIComponent(str: any): string;
    /**
     *
     * @param str
     * @returns {string}
     */
    function fixedDecodeURIComponent(str: any): string;
    /**
     * url encode all dict values
     * @param dict
     * @returns {any}
     */
    function encodeDict(dict: any): any;
    function _canEncode(key: any, value: any): boolean;
    /**
     * decodes the url string
     * @param url
     * @param sortParameters
     * @returns {string}
     */
    function decodeUrl(url: string, sortParameters?: boolean): string;
    /**
     * url decode the dictionary
     * @param dict
     * @returns {any}
     */
    function decodeDict(dict: any): any;
    /**
     * Checks if the given object contains properties which match the pattern and returns a list of keys.
     * @param obj
     * @param pattern
     * @returns {Array}
     */
    function getKeyByPattern(obj: any, pattern: any): any[];
    /**
     * Merges both objects and returns a object which contains every property of both objects where properties of "dominant" have priority.
     *
     * @param recessive
     * @param dominant
     */
    function mergeProperties(recessive: any, dominant: any): any;
    /**
     * Returns the endpath of an url e.g. everything after the last "/".
     * domain.com/index.html?param1=abc would result in "index.html?param1=abc".
     * If the url doesnt contain a context path (e.g. http://domain.de?parameter=value) the parameter string is returned.
     * @param url (OPTIONAL) If not supplied the current window url is used.
     * @returns {string}
     */
    function getCurrentEndPathString(url?: string): string;
    function fireCustomEvent(eventName: string, canBubble: any, cancelable: any, element?: any): void;
    function replaceAt(baseString: any, index: any, character: any): any;
    function removeAt(baseString: any, index: any, length: any): any;
    function randomString(length: any): string;
    function isFFParameter(paramName: any): any;
    var localStorage: {
        store: {};
        setItem: (key: string, value?: string) => void;
        getItem: (key: string) => any;
        isSupported(): boolean;
    };
    /**
     * Removes all child nodes of a node.
     *
     * @param node the parent node from which to remove the children
     *
     * @see: http://jsperf.com/innerhtml-vs-removechild/15
     */
    function removeChildNodes(node: any): void;
    /**
     * Just a helper method for better null checks.
     *
     * @param prop, some property
     * @returns {boolean} true, when not 'null'
     */
    function isOk(prop: any): boolean;
}
declare module scope.libs {
    interface MustacheStatic {
        render(template: string, data: any): string;
    }
    var mustache: MustacheStatic;
}
declare module scope.communication {
    interface SearchResult {
        channel: string;
        groups: Array<AsnGroup>;
        resultCount: number;
        resultStatus: string;
        resultArticleNumberStatus: string;
        searchParams: string;
        searchTime: number;
        simiFirstRecord: number;
        simiLastRecord: number;
        timedOut: boolean;
        breadCrumbTrailItems: Array<BreadCrumbTrailItem>;
        sortsList: Array<SortItem>;
        resultsPerPageList: Array<ResultsPerPageItem>;
        paging: Paging;
    }
    interface ResultsPerPageItem {
        default: boolean;
        searchParams: string;
        selected: boolean;
        value: number;
    }
    interface AsnGroup {
        detailedLinks: number;
        elements: Array<AsnGroupElement>;
        selectedElements: Array<AsnGroupElement>;
        filterStyle: string;
        type: string;
        unit: string;
        showPreviewImages: boolean;
        groupOrder: number;
        name: string;
    }
    interface AsnGroupElement {
        associatedFieldName: string;
        clusterLevel: number;
        name: string;
        selected: boolean;
        searchParams: string;
        previewImageUrl: string;
    }
    interface AsnGroupFilterElement extends AsnGroupElement {
        absoluteMaxValue: number;
        absoluteMinValue: number;
        selectedMaxValue: number;
        selectedMinValue: number;
    }
    interface BreadCrumbTrailItem {
        associatedFieldName: string;
        searchParams: string;
        text: string;
        type: string;
        value: string;
    }
    interface SortItem {
        description: string;
        name: string;
        order: string;
        selected: boolean;
        searchParams: string;
    }
    interface Suggestion {
        hitCount: number;
        imageUrl: string;
        searchParams: any;
        type: string;
        name: string;
    }
    interface ProductsPerPageItem {
        default: boolean;
        searchParams: string;
        selected: boolean;
        value: number;
    }
    interface Paging {
        currentPage: number;
        pageCount: number;
        resultsPerPage: number;
        firstLink: PageLink;
        lastLink: PageLink;
        nextLink: PageLink;
        previousLink: PageLink;
        pageLinks: Array<PageLink>;
    }
    interface PageLink {
        caption: string;
        currentPage: boolean;
        number: number;
        searchParams: string;
    }
    interface Record extends FFRecordFields, DQRecordFields, RecordCommonFields {
        fields: any;
        position: number;
        id: string;
        similarity: number;
    }
    interface RecordCommonFields {
        name: string;
        description: string;
        price: number;
        imageUrl: string;
        detailUrl: string;
    }
    interface FFRecordFields {
        FFAfterSearchReorder: string;
    }
    interface DQRecordFields {
        DQAttributes: string;
    }
    class FFEvent {
        url: string;
        type: string;
        success: any;
        fail: any;
        always: any;
        version: string;
        sid: string;
        query: string;
        channel: string;
        page: string;
        productsPerPage: string;
        sort: string;
        filter: string;
        searchField: string;
        articleNumberSearch: string;
        useASN: string;
        useFoundWords: string;
        useCampaigns: string;
        navigation: string;
        idsOnly: string;
        useKeywords: string;
        generateAdvisorTree: string;
        disableCache: string;
    }
    class FilterEvent extends FFEvent {
        groupName: string;
        filterName: string;
        selectedMaxValue: string;
        selectedMinValue: string;
        removeAll: boolean;
        clusterLevel: number;
    }
    class BreadCrumbEvent extends FFEvent {
        value: string;
    }
    class ProductsPerPageEvent extends FFEvent {
        value: number;
    }
    class PagingEvent extends FFEvent {
        number: number;
    }
    class ProductCampaignEvent extends FFEvent {
        productNumber: number;
    }
    class SuggestEvent extends FFEvent {
    }
    class ProductDetailEvent extends FFEvent {
        id: number;
    }
    class GlobalSearchParameter extends FFEvent {
        constructor();
    }
    class CustomUrls {
        serachUrl: string;
        suggestUrl: string;
        recommendationUrl: string;
        tagCloudUrl: string;
        trackingUrl: string;
        campaignUrl: string;
        compareUrl: string;
        similarRecordsUrl: string;
        getRecordsUrl: string;
    }
    var globalSearchParameter: GlobalSearchParameter;
    var globalCustomUrls: CustomUrls;
    var sp: any;
    module Util {
        /**
         * Returns the parameter string for the current FF searchResult
         * @returns {string}
         */
        function getSearchParamString(): string;
        /**
         * Updates the search event with the nesessary SEO information
         * @param event
         * @private
         */
        function handleSeoSearch(event: any): void;
        function replaceHistoryState(result: any, urlString: string, ffEvent: any): void;
        /**
         * Push the result to the browser history and set's the url params according to the settings in global.
         * Has no effect on a suggest or tracking result.
         *
         * @param result,       the request result object.
         * @param urlString,    the url with which the request was send     .
         * @param ffEvent,      the event from which the request was sourced.
         */
        function pushParameterToHistory(result: any, urlString: any, ffEvent: any): void;
        function getBrowserURL(dict: any, ffEvent: any, removeFFparams: any, whitelist?: string[]): string;
        /**
         * Handel's the history and url on a seo search request.
         *
         * @param result, the request result
         */
        function pushSeoToHistory(result: any): void;
        /**
         * Handel's the browser history on a filterURL setting.
         *
         * @param result, the
         * @param paramsDict
         */
        function pushFilterUrlToHistory(result: any, paramsDict: any): void;
        /**
         *  example changePropertyName([{firstName: "Peter"}], {firstName: "fName"});
         * @param data the data object
         * @param nameMapping object literal containing the old name as property name and the new name as property value
         */
        function changeRecordPropertyName(data: any, nameMapping: any): void;
        /**
         * Returns the query of the currentSearchResult in respect to seoPath.
         *
         * @returns {any}
         */
        function getQueryFromSearchParams(): string;
        function addRenameRecordFields(nameMapping: any): {
            "productCampaign": string;
            "result": string;
            "recommendation": string;
            "similarProducts": string;
        };
        function removeRenameRecordFields(keys: any): void;
        var trackingHelper: {
            getTrackingProductId: (record: any) => any;
            getPrice: (record: any) => any;
            getTitle: (record: any) => any;
            getMasterArticleNumber: (record: any) => any;
            getUserId: () => any;
            getCampaignProductNumber(record: any): any;
        };
    }
    class GlobalCommunicationParameter {
        ajax: Boolean;
        useUrlParameter: Boolean;
        useBrowserHistory: Boolean;
        useCache: Boolean;
        appendUnknownParameter: Boolean;
        isSessionIdDisabled: boolean;
        keepUrlParams: String;
        keepFilters: Boolean;
        addParams: String;
        addTrackingParams: String;
        userId: String;
        useAsn: Boolean;
        useFoundWords: Boolean;
        useCampaigns: Boolean;
        generateAdvisorTree: Boolean;
        disableCache: Boolean;
        usePersonalization: Boolean;
        useSemanticEnhancer: Boolean;
        useAso: Boolean;
        useSeo: Boolean;
        seoPrefix: String;
        sid: string;
        currencyCountryCode: string;
        currencyCode: string;
        currencyFields: string;
        currencyMinDigits: string;
        currencyMaxDigits: string;
        singleHitRedirect: boolean;
        singleHitRedirectBasePath: string;
        useFilterURL: boolean;
        filterUrlPrefix: string;
        onlySearchParams: boolean;
        asyncFacets: boolean;
        parameterWhitelist: string;
        sortUrlParametersAlphabetically: boolean;
    }
    var globalCommunicationParameter: GlobalCommunicationParameter;
    class GlobalElementValues {
        currentFFSearchBoxValue: string;
    }
    var globalElementValues: GlobalElementValues;
    module ResultDispatcher {
        function invokeCallbacks(topic: any, data: any, event: any): void;
        function addCallback(topic: any, fn: any, context: any): string;
        function removeCallback(topic: any, key: any): boolean;
        /**
         * Subscribe for notifications in differnt topics: "result", "records", "asn", "sorting", "paging"
         *
         * @function
         * @name subscribe
         *
         * @param topic, a string value
         * @param fn Callback function for notifications
         *
         * @memberOf ResultDispatcher
         */
        function subscribe(topic: any, fn: any, ctx?: any): string;
        function unsubscribe(topic: any, key: any): Boolean;
        /**
         *  Sets "shouldDeferDispatches" to false and calls all deferred subscribers
         *  (see FFWEB-803 for example reasoning)
         */
        function startDispatching(): void;
        function setShouldDeferDispatches(shouldDefer: any): void;
        /**
         * When no Topics to dispatch are set, then try to dispatch this as a 'searchResult' to all standard handler...
         * @param response
         * @param event
         * @param topics
         */
        function dispatch(response: any, event: any, topics: any): void;
        /**
         * This dispatches a generic topic.
         *
         * @param result, the 'searchResult' of an request
         * @param topic, the topic to dispatch to
         */
        function dispatchResultByTopic(response: any, topic: any, event: any): void;
        /**
         * Dispatch the 'searchResult' to all default result subscribers
         *
         * @function
         * @name dispatchSearchResult
         * @memberOf ResultDispatcher
         *
         * @param result
         */
        function dispatchSearchResult(result: any, event: any): void;
        /**
         * Old method name. Still here for downwards compatibility.
         * AP: rename this to 'dispatchSearchResult'
         * @param result
         * @param event
         */
        function dispatchResult(result: any, event?: any): void;
        /**
         * Dispatches to the result topic.
         * AP: rename this to 'dispatchResult' after the old 'dispatchResult' is renamed.
         *
         * @param result
         */
        function dispatchResultInternal(result: any, event: any): void;
        /**
         * Dispatches only the records from a searchResult to the topic 'records'
         *
         * @param result, a searchResult from a ff response
         */
        function dispatchRecords(result: any, event: any): void;
        /**
         * Dispatches only the 'breadCrumbTrailItems' of a searchResult to the topic 'btc'
         *
         * @param result
         */
        function dispatchBreadcrumbTrail(result: any, event: any): void;
        /**
         * Dispatches only the 'sortsList' of a searchResult to the topic 'sort'
         *
         * @param result
         */
        function dispatchSorting(result: any, event: any): void;
        /**
         * Dispatches only the 'resultsPerPageList' of a searchResult to the topic 'ppp'
         *
         * @param result
         */
        function dispatchProductsPerPage(result: any, event: any): void;
        /**
         * Dispatches only the 'paging' of a searchResult to the topic 'paging'
         *
         * @param result
         */
        function dispatchPaging(result: any, event: any): void;
        /**
         * Dispatches only the 'paging.pageLinks' of a searchResult to the topic 'pagingItems'
         *
         * @param result
         */
        function dispatchPagingItems(result: any, event: any): void;
        /**
         * Dispatches only the 'groups' of a searchResult to the topic 'asn'
         *
         * @param result
         */
        function dispatchAsn(result: any, event: any): void;
        /**
         * Dispatches only the 'singleWordResults' of a searchRequest to the topic 'singleWordSearch'
         *
         * @param result
         */
        function dispatchSingleWordSearch(result: any, event: any): void;
        /**
         * Dispatches only the first element in the 'records' of a searchResult to the topic 'productDetail'
         *
         * @param result
         */
        function dispatchProductDetail(result: any, event: any): void;
        /**
         * This is the method for dispatching campaigns form a searchResult.
         * to the campaign topic's which start with 'campaign'
         *
         * @param result, a searchResult
         */
        function dispatchCampaigns(result: any, event: any): void;
        /**
         * Dispatches the result of a response from 'ProductCampaign.ff/do=getPageCampaigns'
         * to the campaign topic's which start with 'campaign'
         * BUT invoke callbacks for 'pageCampaigns'
         *
         * @param pageCampaigns, an array of 'pageCampaigns'
         */
        function dispatchPageCampaigns(pageCampaigns: any, event: any): void;
        /**
         * Dispatches the result of a response from 'ProductCampaign.ff/do=getProductCampaigns'
         * to the campaign topic's which start with 'productCampaign'
         *
         * @param campaigns, an array of productCampaigns
         */
        function dispatchProductCampaigns(campaigns: any, event: any): void;
        /**
         * Dispatch the result of a response from 'ProductCampaign.ff/do=getShoppingCartCampaigns'
         * to the campaign topic's which start with 'shoppingCartCampaign'
         *
         * @param campaigns, an array of shoppingCartCampaign
         */
        function dispatchShoppingCartCampaign(campaigns: any, event: any): void;
        /**
         * Dispatch campaigns to all subscriber and callbacks for a type:
         *
         * [type]
         *
         * [type]:[name]
         * [type]:[flavor]
         * [type]:[flavor]:[name]
         *
         * [type]:feedbacktext
         * [type]:feedbacktext:[name]
         * [type]:feedbacktext:[name]:[feedBackText.label]
         *
         * [type]:pushedProducts
         * [type]:pushedProducts:[name]
         *
         *
         * @param campaigns
         * @param type
         */
        function _doCampaingDispatch(campaigns: any, type: any, event: any): void;
        /**
         * Dispatches Suggest.ff responses.
         * ex response:
         *  {
                "suggestions": [
                    {
                    "attributes": {
                        "sourceField": "Manufacturer",
                        "Manufacturer_Logo": "jack-links-logo.png"
                    },
                    "hitCount": 0,
                    "image": "",
                    "name": "Jack Links",
                    "searchParams": "/FACT-Finder7.1-Demoshop/Search.ff?query=Jack+Links+*\u0026filterManufacturer=Jack+Links\u0026channel=bergfreunde-de",
                    "type": "brand"
                }, ...
                ]
         * }
         *
         * @param suggestions, this is a Suggest.ff response
         */
        function dispatchSuggest(suggestions: any, event?: any): void;
        /**
         * Dispatches a Recommender.ff response.
         * ex response:
         *   resultRecords: [
         *   ..records..
         *   ];
         * @param recommendations, a Recommender.ff response parameter is a list of records.
         * @param event, the recommendation event with key information.
         */
        function dispatchRecommendations(recommendations: any, event: any): void;
        /**
         * Dispatches a SimilarRecords.ff response.
         * ex response:
         * {
                attributes: [{
                    "name": "Category2",
                    "value": "..Boulderhosen..#..Jeans & Casual..#..Kletterhosen.."
                }, {
                    "name": "Category1",
                    "value": "..Outdoor Hosen..#..Outdoor Hosen..#..Outdoor Hosen.."
                }, {"name": "Category0", "value": "..Outdoor Bekleidung..#..Outdoor Bekleidung..#..Outdoor Bekleidung.."}],
                records: [
                ..records..
                ]
         * }
         *
         * @param similarProducts
         */
        function dispatchSimilarProducts(similarProducts: any, event: any): void;
        /**
         * Dispatches a Compare.ff response.
         * ex response:
         * {
                "attributes": [{"attributeName": "Manufacturer", "different": true, "sourceField": "Manufacturer"}],
                "records": [
                    ..records..
                ]
         * }
         * @param compareResult
         * @param event
         */
        function dispatchCompare(compareResult: any, event: any): void;
        /**
         * Dispatches a TagCould.ff response
         * ex response:
         * resultEntries:[{
                    "params": "/FACT-Finder-7.2/Search.ff?query=aku&channel=bergfreunde-co-uk",
                    "query": "aku",
                    "searchCount": 9,
                    "weight": 0.0
         *      },...
         * ];
         *
         * @param result
         */
        function dispatchTagCloud(result: any, event: any): void;
        /**
         * Dispatches the 'query' on a searchResult to the topic 'query'
         * @param result
         */
        function dispatchQuery(result: any, event: any): void;
        /**
         * The dispatched result may be just a part of the whole navigation.
         * We first add the results to the client side cache and fire an update event with the zero layer for the ff-navigation to initialize.
         *
         * @param result
         * @param event
         */
        function dispatchNavigationFrame(result: any, event: any): void;
    }
    class SessionManager {
        LOCALSTORAGE_SID_PARAM_NAME: string;
        REQUEST_SID_PARAM_NAME: string;
        SESSION_ID_LENGTH: number;
        checkSession(event: any): void;
        /**
         * Reads the sid from localstorage. If create parameter is true, a new session is created if, and only if, no older(localstorage) session exists.
         * @param create
         * @returns {any}
         */
        readSessionIdFromLocalstorage(create?: Boolean): any;
        /**
         * Writes the given parameter or the localstorage sid or a newly created sid to the current url.
         * @param sessionId
         */
        writeSessionIdToUrl(sessionId?: string): void;
        readSessionIdFromRequestParams(create?: Boolean): any;
    }
    var sessionManager: SessionManager;
    class Tracking12 {
        private requestBuilder;
        private sessionManager;
        constructor();
        neededClickInfo: string[];
        neededCartInfo: string[];
        neededCheckoutInfo: string[];
        neededRecoInfo: string[];
        neededLoginInfo: string[];
        neededSearchFeedbackInfo: string[];
        /**
         * Needed: id, query, pos, origPos, page, origPageSize
         * Optional: masterId, title, userId, pageSize, simi
         * @param event
         */
        click(event: any): void;
        /**
         * Needed: id, count
         * Optional: masterId, title, userId, price
         * @param event
         */
        cart(event: any): void;
        /**
         * Needed: id, count
         * Optional: masterId, title, userId, price
         * @param event
         */
        checkout(event: any): void;
        login(event: any): void;
        /**
         * Needed: id, mainId
         * Optional: masterId, title, userId
         * @param event
         */
        recommendationClick(event: any): void;
        searchFeedback(event: any): void;
        checkNeededParamsAvailable(needed: any, check: any): boolean;
        _sendRequest(event: any): void;
    }
    var trackingManager: Tracking12;
    var eventFunctions: {};
    class FFCommunicationHandler {
        /**
         * Fires a FACTFinder Event.
         *
         * @param event, the event to fire.
         */
        addFFEvent(event: scope.communication.FFEvent): void;
    }
    class FF611CommunicationHandler extends scope.communication.FFCommunicationHandler {
    }
    /**
     * Created by tobias.armbruster on 13.04.2016.
     */
    class FF70CommunicationHandler extends scope.communication.FFCommunicationHandler {
    }
    /**
     * Created by tobias.armbruster on 13.04.2016.
     */
    class FF71CommunicationHandler extends scope.communication.FFCommunicationHandler {
    }
    module FFCommunicationEventAggregator {
        var currentSearchResult: any;
        var navigationResult: any;
        var navigationLayer: any;
        var beforeHistoryPushCallbacks: any[];
        /**
         * IMPORTANT !!!!
         * Alle Parameter außer query, url, sid und type werden grundsätzlich encoded.
         *
         * @param event
         */
        function addFFEvent(event: any): void;
        function removeFailCallback(key: any): boolean;
        function addBeforeHistoryPushCallback(fn: any): string;
        function removeBeforeHistoryPushCallback(key: any): boolean;
        function addFailCallback(fn: any): string;
        function removeBeforeDispatchingCallback(key: any): boolean;
        function addBeforeDispatchingCallback(fn: any): string;
        function getCurrentResult(version: any): any;
    }
}
declare module scope.middleware.response {
    /**
     * Register a middleware module to the response dispatch chain to manipulate the response before being emitted.
     * @param module
     */
    function use(module: any): void;
}
declare module scope.middleware.response {
    /**
     * This modifier is used to transform multi-attribute fields in the FACT-Finder response to JavaScript objects.
     * Values and units can then be accessed via their key.
     * @param options
     */
    function MultiAttributeParsing(options: any): {
        topic: string;
        handler: (data: any) => void;
    };
}
declare module scope.html {
    class RendererOptions {
        append: boolean;
        returnAsString: boolean;
    }
    class TemplateRenderer {
        renderer: libs.MustacheStatic;
        render(template: any, data: any, containerElement?: HTMLElement, options?: RendererOptions): any;
    }
}
declare module scope.html {
    class AsnRenderer {
        templateRenderer: TemplateRenderer;
        /**
         *
         * @param templates
         * @param ansGroupElement
         * @param asnGroup
         * @param containerElement
         * @param options
         * @returns {string}
         */
        processAsnElement(templates: any, ansGroupElement: scope.communication.AsnGroupElement, asnGroup: scope.communication.AsnGroup, containerElement?: HTMLElement, options?: scope.html.RendererOptions): any;
        /**
         *
         * @param templates
         * @param asnGroup
         * @param containerElement
         * @returns {HTMLElement}
         */
        processDetailedLinks(templates: any, asnGroup: scope.communication.AsnGroup, containerElement?: any, options?: scope.html.RendererOptions): any;
        /**
         *
         * @param templates
         * @param asnGroup
         * @param containerElement
         * @returns {*}
         */
        processHiddenLinks(templates: any, asnGroup: scope.communication.AsnGroup, containerElement?: any, options?: scope.html.RendererOptions): any;
    }
}
declare var factfinder: any;
