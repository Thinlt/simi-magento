/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer store credit(balance) application
 */
/*global define,alert*/
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/error-processor',
        'Vnecoms_Credit/js/model/payment/credit-messages',
        'mage/storage',
        'Magento_Checkout/js/action/get-totals',
        'mage/translate',
        'Magento_Checkout/js/model/payment/method-list',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals',
        'priceUtils'
    ],
    function (
        ko,
        $,
        quote,
        urlManager,
        paymentService,
        errorProcessor,
        messageContainer,
        storage,
        getTotalsAction,
        $t,
        paymentMethodList,
        getPaymentInformationAction,
        totals,
        utils        
    ) {
        'use strict';
        return function (baseCreditAmount, creditAmount, isApplied, isLoading) {
            var quoteId = quote.getQuoteId();
            var params = (urlManager.getCheckoutMethod() == 'guest') ? {quoteId: quote.getQuoteId()} : {};
            var urls = {
                'default': '/carts/mine/credit/'+baseCreditAmount()
            };
            
            var url = urlManager.getUrl(urls,params);
            var message = $t('%1 credit was successfully applied');
            return storage.put(
                url,
                {},
                false
            ).done(
                function (response) {
                    if (response) {
                        var deferred = $.Deferred();
                        isLoading(false);
                        isApplied(true);
                        baseCreditAmount(Math.abs(response[0]));
                        creditAmount(Math.abs(response[1]));
                        /*getTotalsAction([], deferred);
                        $.when(deferred).done(function() {
                            paymentService.setPaymentMethods(
                                paymentMethodList()
                            );
                        });*/
                        totals.isLoading(true);
                        getPaymentInformationAction(deferred);
                        $.when(deferred).done(function () {
                            isApplied(true);
                            totals.isLoading(false);
                        });
                        
                        var baseCreditAmountFormated = utils.formatPrice(baseCreditAmount(), quote.getBasePriceFormat());
                        message = message.replace("%1",baseCreditAmountFormated);
                        messageContainer.addSuccessMessage({'message': message});
                    }
                }
            ).fail(
                function (response) {
                    isLoading(false);
                    errorProcessor.process(response, messageContainer);
                }
            );
        };
    }
);
