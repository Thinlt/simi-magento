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
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/error-processor',
        'Vnecoms_Credit/js/model/payment/credit-messages',
        'mage/storage',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals',
        'mage/translate'
    ],
    function ($, quote, urlManager, errorProcessor, messageContainer, storage, getPaymentInformationAction, totals, $t) {
        'use strict';

        return function (isApplied, isLoading) {
            var quoteId = quote.getQuoteId(),
	            params = (urlManager.getCheckoutMethod() == 'guest') ? {quoteId: quote.getQuoteId()} : {},
	            urls = {
	                'default': '/carts/mine/credit'
	            },
                message = $t('Your credit was successfully removed');
	        var url = urlManager.getUrl(urls,params);
            messageContainer.clear();

            return storage.delete(
                url,
                false
            ).done(
                function () {
                    var deferred = $.Deferred();
                    totals.isLoading(true);
                    getPaymentInformationAction(deferred);
                    $.when(deferred).done(function () {
                        isApplied(false);
                        totals.isLoading(false);
                        $("#use-store-credit").attr('checked',null);
                    });
                    messageContainer.addSuccessMessage({
                        'message': message
                    });
                }
            ).fail(
                function (response) {
                    totals.isLoading(false);
                    errorProcessor.process(response, messageContainer);
                }
            ).always(
                function () {
                    isLoading(false);
                }
            );
        };
    }
);
