
require(["jquery"], function($){
    //jquery v1.11

    (function() {

        'use strict';

        var redirectPath = '/FACT-Finder/result';
        var query_minLength = 1;

        var searchBoxEl;

        $(document).ready(function() {

            searchBoxEl = $("input[is='ff-searchbox']").first();

            searchBoxEl.keypress(function (e) {

                if (e.which == 13) { //enterKey pressed
                    ffRedirect();
                    return false;
                }

            });

            $("[is='ff-searchbutton']").each(function(){

                $(this).on( "click", function() {
                    ffRedirect();
                });

            });

        });

        window.ffRedirect = function() {

            var urlArr = window.location.href.split("/");
            var domainUrl = urlArr[0] + "//" + urlArr[2];

            var query = searchBoxEl.val();

            if(query.length >= query_minLength) {
                window.location.href = domainUrl + redirectPath + '?query=' + encodeURIComponent(query);
            }

        }

    })(this);


});
