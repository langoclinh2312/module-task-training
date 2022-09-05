/*global define*/
define(
    [
        'AHT_ProductFee/js/view/cart/summary/fee'
    ],
    function(Component) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'AHT_ProductFee/cart/totals/fee'
            },
            /**
             * @override
             *
             * @returns {boolean}
             */
            isDisplayed: function() {
                return this.getPureValue() != 0;
            }
        });
    }
);