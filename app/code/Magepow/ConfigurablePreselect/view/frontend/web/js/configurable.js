/**
 * Copyright © Magepow. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Mixin for Magento_ConfigurableProduct/js/configurable
 * Preselects the first option in configurable product dropdowns.
 */
define([
    'jquery',
    'priceUtils'
], function ($, priceUtils) {
    'use strict';

    return function (configurable) {
        $.widget('mage.configurable', $['mage']['configurable'], {
            /**
             * Fill a select element with attribute options, auto-selecting the first option.
             *
             * @param {HTMLElement} element
             */
            _fillSelect: function (element) {
                var attributeId = element.id.replace(/[a-z]*/, ''),
                    options = this._getAttributeOptions(attributeId),
                    prevConfig,
                    index = 1,
                    allowedProducts,
                    i,
                    j,
                    finalPrice = parseFloat(this.options.spConfig.prices.finalPrice.amount),
                    optionFinalPrice,
                    optionPriceDiff,
                    optionPrices = this.options.spConfig.optionPrices,
                    allowedProductMinPrice;

                this._clearSelect(element);
                element.options[0] = new Option('', '');
                element.options[0].innerHTML = this.options.spConfig.chooseText;
                prevConfig = false;

                if (element.prevSetting) {
                    prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
                }

                if (options) {
                    for (i = 0; i < options.length; i++) {
                        allowedProducts = [];
                        optionPriceDiff = 0;

                        /* eslint-disable max-depth */
                        if (prevConfig) {
                            for (j = 0; j < options[i].products.length; j++) {
                                if (prevConfig.config &&
                                    prevConfig.config.allowedProducts &&
                                    prevConfig.config.allowedProducts.indexOf(options[i].products[j]) > -1) {
                                    allowedProducts.push(options[i].products[j]);
                                }
                            }
                        } else {
                            allowedProducts = options[i].products.slice(0);

                            if (typeof allowedProducts[0] !== 'undefined' &&
                                typeof optionPrices[allowedProducts[0]] !== 'undefined') {
                                allowedProductMinPrice = this._getAllowedProductWithMinPrice(allowedProducts);
                                optionFinalPrice = parseFloat(optionPrices[allowedProductMinPrice].finalPrice.amount);
                                optionPriceDiff = optionFinalPrice - finalPrice;

                                if (optionPriceDiff !== 0) {
                                    options[i].label = options[i].label + ' ' + priceUtils.formatPrice(
                                        optionPriceDiff,
                                        this.options.priceFormat,
                                        true
                                    );
                                }
                            }
                        }

                        if (allowedProducts.length > 0) {
                            options[i].allowedProducts = allowedProducts;
                            element.options[index] = new Option(this._getOptionLabel(options[i]), options[i].id);

                            if (typeof options[i].price !== 'undefined') {
                                element.options[index].setAttribute('price', options[i].price);
                            }

                            element.options[index].config = options[i];
                            index++;
                        }

                        if (i === 0) {
                            this.options.values[attributeId] = options[i].id;
                        }
                        /* eslint-enable max-depth */
                    }
                }
            }
        });

        return $['mage']['configurable'];
    };
});
