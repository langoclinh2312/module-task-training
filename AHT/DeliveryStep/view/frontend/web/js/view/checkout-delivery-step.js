define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'mage/url',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/error-processor',
        'mage/url',
        'Magento_Checkout/js/model/cart/cache'
    ],
    function($, ko, Component, _, stepNavigator, urlBuilder, urlBuilderCheckout, customer, quote, errorProcessor, urlFormatter, cartCache) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'AHT_DeliveryStep/delivery-step'
            },

            //add here your logic to display step,
            isVisible: ko.observable(true),

            //step code will be used as step content id in the component template
            stepCode: 'deliverystep',

            //step title value
            stepTitle: 'Delivery Step',

            /**
             *
             * @returns {*}
             */
            initialize: function() {
                this._super();
                // register your step
                stepNavigator.registerStep(
                    this.stepCode,
                    //step alias
                    null,
                    this.stepTitle,
                    //observable property with logic when display step or hide step
                    this.isVisible,

                    _.bind(this.navigate, this),

                    /**
                     * sort order value
                     * 'sort order value' < 10: step displays before shipping step;
                     * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                     * 'sort order value' > 20 : step displays after payment step
                     */
                    15
                );

                return this;
            },

            /**
             * The navigate() method is responsible for navigation between checkout step
             * during checkout. You can add custom logic, for example some conditions
             * for switching to your custom step
             */
            navigate: function() {
                this.isVisible(true);
            },

            /**
             * @returns void
             */
            navigateToNextStep: function() {
                const isCustomer = customer.isLoggedIn();
                const quoteId = quote.getQuoteId();
                const url = urlFormatter.build('delivery_step/quote/save');
                const valueDeliveryDate = $('[name="delivery_date"]').val();
                if (valueDeliveryDate) {
                    const payload = {
                        'cartId': quoteId,
                        'delivery_date': valueDeliveryDate,
                        'delivery_comment': $('[name="delivery_comment"]').val(),
                        'is_customer': isCustomer
                    };

                    if (!payload.delivery_date) {
                        return true;
                    }

                    var result = true;

                    $.ajax({
                        url: url,
                        data: payload,
                        dataType: 'text',
                        type: 'POST',
                    }).done(
                        function(response) {
                            result = true;
                        }
                    ).fail(
                        function(response) {
                            result = false;
                            errorProcessor.process(response);
                        }
                    );
                }
                if (result) {
                    stepNavigator.next();
                }
            }
        });
    });