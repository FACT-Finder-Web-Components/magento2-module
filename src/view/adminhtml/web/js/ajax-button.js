define(['jquery', 'Magento_Ui/js/modal/alert'], ($, alert) => {
    $.widget('omikron.ajaxButton', {
        options: {
            pattern: /fields\]\[([^\]]*)\]\[value/
        },

        _create: function () {
            var self = this;
            this._on(this.element, {
                'click': () => {
                    $.ajax({
                        data: self.getFields(),
                        dataType: 'json',
                        showLoader: true,
                        type: 'POST',
                        url: self.options.url
                    }).done((response) => {
                        alert({title: '', content: response.message});
                    });
                }
            });
        },

        getFields: function () {
            var pattern = this.options.pattern;
            var inputs = Array.from(this.element.closest('fieldset').find('input, select')).filter(field => !field.name.includes('_inherit'));

            return inputs.reduce((acc, input) =>  {
                if (pattern.test(input.name)) acc[input.name.match(pattern)[1]] = input.value;
                return acc;
            }, {});
        }
    });

    return $.omikron.ajaxButton;
});
