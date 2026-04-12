/**
 * Copyright © Magepow. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Mixin for Magento_Swatches/js/swatch-renderer
 * Automatically preselects swatch options based on configuration:
 *   type 1 = Default (first option)
 *   type 2 = Highest price
 *   type 3 = Lowest price
 */
define([
    'jquery',
    'underscore',
    'jquery-ui-modules/widget'
], function ($, _) {
    'use strict';

    return function (SwatchRenderer) {
        $.widget('mage.SwatchRenderer', $['mage']['SwatchRenderer'], {
            /**
             * Initialize widget and trigger preselection based on config type.
             *
             * @private
             */
            _init: function () {
                var type = this.options.typeConfigurablePreselectedProduct;

                if (_.isEmpty(this.options.jsonConfig.images)) {
                    this.options.useAjax = true;
                    this._debouncedLoadProductMedia = _.debounce(this._LoadProductMedia.bind(this), 500);
                }

                if (this.options.jsonConfig !== '' && this.options.jsonSwatchConfig !== '') {
                    this.options.jsonConfig.mappedAttributes = _.clone(this.options.jsonConfig.attributes);
                    this._sortAttributes();
                    this._RenderControls();

                    if (type === '2') {
                        this._preselectByPrice('highest');
                    } else if (type === '3') {
                        this._preselectByPrice('lowest');
                    } else if (type === '1') {
                        this._preselectDefault();
                    }

                    this._setPreSelectedGallery();
                    $(this.element).trigger('swatch.initialized');
                } else {
                    console.log('SwatchRenderer: No input data received');
                }

                this.options.tierPriceTemplate = $(this.options.tierPriceTemplateSelector).html();
            },

            /**
             * Preselect the first available swatch option for each attribute.
             *
             * @private
             */
            _preselectDefault: function () {
                var selectswatch;

                if ($('.swatch-attribute').length > 0) {
                    selectswatch = this.element.find(
                        '.' + this.options.classes.attributeClass +
                        ' .' + this.options.classes.attributeOptionsWrapper
                    );

                    $.each(selectswatch, function (index, item) {
                        var swatchOption = $(item).find('div.swatch-option').first();

                        if (swatchOption.length && !$(item).find('div.swatch-option').hasClass('selected')) {
                            swatchOption.trigger('click');
                        }
                    });
                }
            },

            /**
             * Preselect the swatch option with the highest or lowest product price.
             *
             * @param {string} strategy - 'highest' or 'lowest'
             * @private
             */
            _preselectByPrice: function (strategy) {
                var mcsWidget = this,
                    productId,
                    selectswatch,
                    targetPrice = strategy === 'lowest' ? Infinity : 0;

                if ($('.swatch-attribute').length === 0) {
                    return;
                }

                selectswatch = this.element.find(
                    '.' + this.options.classes.attributeClass +
                    ' .' + this.options.classes.attributeOptionsWrapper
                );

                $.each(this.options.jsonConfig.attributes, function () {
                    var item = this;

                    $.each(item.options, function () {
                        var opt = this;

                        $.each(opt.products, function () {
                            var prd = this,
                                prdPrices = mcsWidget.options.jsonConfig.optionPrices[prd],
                                amount;

                            if (prdPrices) {
                                amount = prdPrices.finalPrice.amount;

                                if (strategy === 'highest' && amount > targetPrice) {
                                    targetPrice = amount;
                                    productId = prd;
                                } else if (strategy === 'lowest' && amount < targetPrice) {
                                    targetPrice = amount;
                                    productId = prd;
                                }
                            }
                        });
                    });
                });

                if (productId) {
                    this._triggerSwatchForProduct(productId, selectswatch);
                }
            },

            /**
             * Trigger click on the swatch options matching the given product ID.
             *
             * @param {string} productId
             * @param {jQuery} selectswatch
             * @private
             */
            _triggerSwatchForProduct: function (productId, selectswatch) {
                $.each(this.options.jsonConfig.attributes, function () {
                    var item = this;

                    $.each(item.options, function () {
                        var opt = this;

                        $.each(opt.products, function () {
                            var prd = this;

                            if (prd === productId) {
                                $.each(selectswatch, function (index, swatchItem) {
                                    var swatchOption = $(swatchItem).find(
                                        'div[data-option-id="' + opt.id + '"]'
                                    );

                                    if (swatchOption.length &&
                                        !$(swatchItem).find('div.swatch-option').hasClass('selected')) {
                                        swatchOption.trigger('click');
                                    }
                                });
                            }
                        });
                    });
                });
            }
        });

        return $['mage']['SwatchRenderer'];
    };
});
