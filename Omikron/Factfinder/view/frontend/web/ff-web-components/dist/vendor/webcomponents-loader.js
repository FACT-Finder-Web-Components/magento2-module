!function(){"use strict";var n,t=!1,o=[],d=!1;function e(){window.WebComponents.ready=!0,document.dispatchEvent(new CustomEvent("WebComponentsReady",{bubbles:!0}))}function i(){window.customElements&&customElements.polyfillWrapFlushCallback&&customElements.polyfillWrapFlushCallback(function(e){n=e,d&&n()})}function r(){window.HTMLTemplateElement&&HTMLTemplateElement.bootstrap&&HTMLTemplateElement.bootstrap(window.document),t=!0,c().then(e)}function c(){d=!1;return Promise.all(o.map(function(e){return e instanceof Function?e():e})).then(function(){d=!0,o.length=0,n&&n()})["catch"](function(e){console.error(e)})}window.WebComponents=window.WebComponents||{},window.WebComponents.ready=window.WebComponents.ready||!1,window.WebComponents.waitFor=window.WebComponents.waitFor||function(e){e&&(o.push(e),t&&c())},window.WebComponents._batchCustomElements=i;var l="webcomponents-loader.js",a=[];(!("attachShadow"in Element.prototype&&"getRootNode"in Element.prototype)||window.ShadyDOM&&window.ShadyDOM.force)&&a.push("sd"),window.customElements&&!window.customElements.forcePolyfill||a.push("ce");var s=function(){var e=document.createElement("template");if(!("content"in e))return!0;if(!(e.content.cloneNode()instanceof DocumentFragment))return!0;var n=document.createElement("template");n.content.appendChild(document.createElement("div")),e.content.appendChild(n);var t=e.cloneNode(!0);return 0===t.content.childNodes.length||0===t.content.firstChild.content.childNodes.length}();if(window.Promise&&Array.from&&window.URL&&window.Symbol&&!s||(a=["sd-ce-pf"]),a.length){var m,w="bundles/webcomponents-"+a.join("-")+".js";if(window.WebComponents.root)m=window.WebComponents.root+w;else{var u=document.querySelector('script[src*="'+l+'"]');m=u.src.replace(l,w)}var p=document.createElement("script");p.src=m,"loading"===document.readyState?(p.setAttribute("onload","window.WebComponents._batchCustomElements()"),document.write(p.outerHTML),document.addEventListener("DOMContentLoaded",r)):(p.addEventListener("load",function(){i(),r()}),p.addEventListener("error",function(){throw new Error("Could not load polyfill bundle"+m)}),document.head.appendChild(p))}else t=!0,"complete"===document.readyState?e():(window.addEventListener("load",r),window.addEventListener("DOMContentLoaded",function(){window.removeEventListener("load",r),r()}))}();