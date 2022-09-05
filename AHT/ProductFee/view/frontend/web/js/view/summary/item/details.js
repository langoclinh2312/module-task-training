/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'uiComponent'
    ],
    function(Component) {
        "use strict";

        var quoteItemData = window.checkoutConfig.quoteItemData;

        return Component.extend({
            defaults: {
                template: 'AHT_ProductFee/summary/item/details'
            },

            quoteItemData: quoteItemData,

            /**
             * @param {Object} quoteItem
             * @return {String}
             */
            getValue: function(quoteItem) {
                return quoteItem.name;
            },

            /**
             * @param {Object} quoteItem
             * @return {Object}
             */
            getAttributeProduct: function(quoteItem) {
                let item = this.getItem(quoteItem.item_id);
                return item;
            },

            /**
             * @param {String} item_id
             * @return {Object}
             */
            getItem: function(item_id) {
                let itemElement = null;
                _.each(this.quoteItemData, function(element, index) {
                    if (element.item_id == item_id) {
                        itemElement = element;
                    }
                });
                return itemElement;
            }
        });
    }
);