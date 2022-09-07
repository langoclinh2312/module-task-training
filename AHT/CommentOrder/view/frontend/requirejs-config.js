var config = {
    map: {
        '*': {
            'Magento_Checkout/js/view/shipping': 'AHT_CommentOrder/js/view/shipping'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'AHT_CommentOrder/js/mixin/place-order-mixin': true
            }
        }
    }
};