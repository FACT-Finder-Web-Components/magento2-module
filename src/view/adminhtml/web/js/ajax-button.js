define(['jquery', 'Magento_Ui/js/modal/alert'], function ($, alert) {
    $.widget('omikron.ajaxButton', {
        options: {
            pattern: /fields\]\[([^\]]*)\]\[value/
        },

        _create: function () {
            var self = this;
            this._on(this.element, {
                'click': function () {
                    $.ajax({
                        data: self.getFields(),
                        dataType: 'json',
                        showLoader: true,
                        type: 'POST',
                        url: self.options.url
                    }).done(function (response) {
                        alert({title: '', content: response.message});
                    });
                }
            });
        },

        getFields: function () {
            var pattern = this.options.pattern;
            return this.element.closest('fieldset').serializeArray().reduce(function (fields, field) {
                fields[field.name.match(pattern)[1]] = field.value;
                return fields;
            }, {});
        }
    });

    return $.omikron.ajaxButton;
});
