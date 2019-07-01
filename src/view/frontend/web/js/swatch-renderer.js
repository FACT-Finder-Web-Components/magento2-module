define(['underscore'], function (_) {
    'use strict';

    document.body.addEventListener('click', function ({target}) {
        if (target.classList.contains('swatch-option')) {
            let optionId = target.getAttribute('option-id'),
                attributeId = target.getAttribute('attribute-id'),
                attributeSelector = target.getAttribute('product-id') + '-' + attributeId,
                notSelected;

            target.classList.add('selected');
            document.getElementById(attributeSelector).value = optionId;
            notSelected = [...document.querySelectorAll('.swatch-option')].filter(function (el) {
                return el.getAttribute('option-id') !== optionId && el.getAttribute('attribute-id') === attributeId;
            });

            notSelected.forEach(function (el) {
                el.classList.remove('selected');
            })
        }
    }, false);

    /**
     *
     * @param attributeLabel
     * @param attributeId
     * @param innerFn
     * @returns {string}
     * @private
     */
    function _renderAttributeContainer(attributeLabel, attributeId, innerFn) {
        return `<div class="swatch-attribute ${attributeId}" attribute-id="${attributeId}">
            <span class="swatch-attribute-label">${attributeLabel}</span>
            <div class="swatch-attribute-options clearfix">`
            + innerFn() + '</div></div>'
    }

    /**
     *
     * @param product_id
     * @returns {string}
     * @private
     */
    function _renderProductInput(product_id) {
        return `<input type="hidden" name="product" value="${product_id}"/>`;
    }

    /**
     *
     * @param attribute_id
     * @param product_id
     * @returns {string}
     * @private
     */
    function _renderOptionInput({attribute_id, product_id}) {
        return `<input type="hidden" id=${product_id + '-' + attribute_id} name="super_attribute[${attribute_id}]"/>`;
    }

    /**
     *
     * @param type
     * @param product_id
     * @param value
     * @param attribute_id
     * @param option_id
     * @returns {string}
     * @private
     */
    function _renderSwatch({type, product_id, value, attribute_id, option_id}) {
        let isColor = (parseInt(type) === 1),
            style = isColor ? `background: ${value} no-repeat center; background-size: initial;` : '';
        return `<div class="swatch-option ${isColor ? 'color' : 'text'}" option-id="${option_id}" attribute-id="${attribute_id}"
            product-id="${product_id}" style="${style}">${!isColor ? value : ''}</div>`;
    }

    /**
     *
     * @returns {function(*): string}
     * @private
     */
    function _sortAttributes(attributes) {
        return _.sortBy(attributes, function (attribute) {
            return parseInt(attribute.position, 10);
        });
    }

    /**
     * Render swatches for single product in record list.
     *
     * @param {Object} record - FACT-Finder search result single record object
     * @return {String}
     */
    return function (record) {
        let html, rawSwatchData, attribute_id, optionId, attribute_label, optionData, options;
        try {
            rawSwatchData = JSON.parse(record.Swatches);
            html = Object.entries(_sortAttributes(rawSwatchData.attributes)).reduce(function (html, attribute) {
                [, {attribute_label, attribute_id, options}] = attribute;
                html += _renderAttributeContainer(attribute_label, attribute_id, function () {
                    html = Object.entries(options).reduce(function (optionsHtml, option) {
                        [optionId, optionData] = option;
                        optionsHtml += _renderSwatch(optionData);
                        return optionsHtml;
                    }, '');
                    return html += _renderOptionInput(optionData);
                });
                return html + _renderProductInput(rawSwatchData.product_id);
            }, '');
        } catch (e) {
            html = _renderProductInput(record.MagentoId);
        }

        return html;
    }
});
