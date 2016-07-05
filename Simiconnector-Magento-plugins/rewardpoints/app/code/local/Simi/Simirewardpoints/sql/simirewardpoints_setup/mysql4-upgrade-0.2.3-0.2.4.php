<?php

/**
 * Simi
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Simi.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simi.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @copyright   Copyright (c) 2012 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$installer->getConnection()->addColumn($this->getTable('simirewardpoints/rate'), 'status', 'SMALLINT(6) UNSIGNED DEFAULT 0');
$installer->endSetup();

$policyPage = Mage::getModel('cms/page')->checkIdentifier('simirewardpoints-policy', 0);
if($policyPage){
    Mage::getModel('cms/page')->load($policyPage)->delete();
}
$welcomePage = Mage::getModel('cms/page')->checkIdentifier('simirewardpoints-welcome', 0);
if($welcomePage){
    Mage::getModel('cms/page')->load($welcomePage)->delete();
}

$policycmsPageData = array(
    'title' => 'Reward Policy',
    'root_template' => 'two_columns_left',
    'meta_keywords' => 'reward policy',
    'meta_description' => 'reward policy',
    'identifier' => 'simirewardpoints-policy',
    'content_heading' => 'Reward Policy',
    'stores' => array(0), //available for all store views
    'is_active' => '1',
    'content' => '<div>
    {{block type="simirewardpoints/template" template="simirewardpoints/policy/earn.phtml" }}
    <div class="simirewardpoints-dashboard-list">
        <strong class="simirewardpoints-title">{{block type="simirewardpoints/welcome_name"}} EXCHANGE RATES</strong>
        <br>
        <p class="simirewardpoints-title-content">The value of {{block type="simirewardpoints/welcome_name"}} is determined by an exchange rate of both currency spent on products to {{block type="simirewardpoints/welcome_name"}}, and an exchange rate of {{block type="simirewardpoints/welcome_name"}} earned to currency for spending on future purchases.</p>
    </div>
    <div class="simirewardpoints-dashboard-list">
        <strong class="simirewardpoints-title">REDEEM {{block type="simirewardpoints/welcome_name"}}</strong>
        <br>
        <p class="simirewardpoints-title-content">You can redeem your {{block type="simirewardpoints/welcome_name"}} at checkout. If you have accumulated enough {{block type="simirewardpoints/welcome_name"}} to redeem them you will have the option of using {{block type="simirewardpoints/welcome_name"}} as one of the payment methods. The option to use {{block type="simirewardpoints/welcome_name"}}, as well as your balance and the monetary equivalent this balance, will be shown to you in the Payment Method area of the checkout. Redeemable {{block type="simirewardpoints/welcome_name"}} can be used in conjunction with other payment methods such as credit cards, gift cards and more.</p>
    </div>
    <div class="simirewardpoints-dashboard-list">
        <strong class="simirewardpoints-title">{{block type="simirewardpoints/welcome_name"}} MINIMUMS AND MAXIMUMS</strong>
        <p class="simirewardpoints-title-content">{{block type="simirewardpoints/welcome_name"}} may be capped at a minimum value required for redemption. If this option is selected you will not be able to use your {{block type="simirewardpoints/welcome_name"}} until you accrue a minimum number of {{block type="simirewardpoints/welcome_name"}}, at which {{config path="simirewardpoints/general/point_name"}} they will become available for redemption.
</br>{{block type="simirewardpoints/welcome_name"}} may also be capped at the maximum value of {{block type="simirewardpoints/welcome_name"}} which can be accrued. If this option is selected you will need to redeem your accrued {{block type="simirewardpoints/welcome_name"}} before you are able to earn more {{block type="simirewardpoints/welcome_name"}}.
</p>
    </div>
    <div class="simirewardpoints-dashboard-list">
        <strong class="simirewardpoints-title">MANAGE YOUR {{block type="simirewardpoints/welcome_name"}}</strong>
        <p class="simirewardpoints-title-content">You have the ability to view and manage your {{block type="simirewardpoints/welcome_name"}} through your <a href="">Customer Account</a>. From your account you will be able to view your total {{block type="simirewardpoints/welcome_name"}} (and currency equivalent), minimum needed to redeem, whether you have reached the maximum {{block type="simirewardpoints/welcome_name"}} limit and a cumulative history of {{block type="simirewardpoints/welcome_name"}} acquired, redeemed and lost. The history record will retain and display historical rates and currency for informational purposes. The history will also show you comprehensive informational messages regarding {{block type="simirewardpoints/welcome_name"}}, including expiration notifications.
</p>
    </div>
    <div class="simirewardpoints-dashboard-list">
        <strong class="simirewardpoints-title">{{block type="simirewardpoints/welcome_name"}} EXPIRATION</strong>
        <p class="simirewardpoints-title-content">{{block type="simirewardpoints/welcome_name"}} can be set to expire. Points will expire in the order form which they were first earned.</p>
        <strong>Note:</strong>
        <ul class="simirewardpoints-dashboard-ul">
            <li>Points can be used as store credit in our system only. Redeeming to cash is not allowed.</li>
            <li>You can sign up to receive email notifications each time your balance changes when you either earn, redeem or lose {{block type="simirewardpoints/welcome_name"}}, as well as point expiration notifications. This option is found in the {{block type="simirewardpoints/welcome_name"}} section of the My Account area.</li>
        </ul>
    </div>
</div>

',
);
Mage::getModel('cms/page')->setData($policycmsPageData)->save();

$welcomecmsPageData = array(
    'title' => 'Reward Welcome Page',
    'root_template' => 'two_columns_left',
    'meta_keywords' => 'reward welcome page',
    'meta_description' => 'reward welcome page',
    'identifier' => 'simirewardpoints-welcome',
    'content_heading' => 'WELCOME TO OUR REWARD PROGRAM!',
    'stores' => array(0), //available for all store views
    'is_active' => '1',
    'content' => '<div>
    <div class="simirewardpoints-dashboard-list">
        Every of your activity on our site is appreciated & rewarded. The more you spend, the more you save. Enroll now to begin earning greater benefits! 
        If you are already a member, log in to view your Reward Balance.
    </div>
    <div class="simirewardpoints-dashboard-list">
        <strong class="simirewardpoints-title simirewardpoints-title-upercase">BENEFITS OF {{block type="simirewardpoints/welcome_name"}} FOR REGISTERED CUSTOMERS</strong>
        <p class="simirewardpoints-title-content"><img src="{{media url="simirewardpoints/default/welcome.png"}}" alt="{{block type="simirewardpoints/welcome_name"}} Welcome" /></p>

        <p class="simirewardpoints-title-content">
            Once you register you will be able to earn and accrue {{block type="simirewardpoints/welcome_name"}}, which are then redeemable at time of purchase towards the cost of your order. Rewards are an added bonus to your shopping experience on the site and just one of the ways we thank you for being a loyal customer. You can easily earn points for certain actions you take on the site, such as making purchases.
        </p>
    </div>
    <div class="simirewardpoints-dashboard-list">
        {{block type="simirewardpoints/account_dashboard_earn" template="simirewardpoints/account/dashboard/earn.phtml"}}
        {{block type="simirewardpointsrule/account_dashboard_earn" template="simirewardpointsrule/account/dashboard/earn.phtml"}}
    </div>
    <div class="simirewardpoints-dashboard-list">
        {{block type="simirewardpoints/account_dashboard_spend" template="simirewardpoints/account/dashboard/spend.phtml"}}
        {{block type="simirewardpointsrule/account_dashboard_spend" template="simirewardpointsrule/account/dashboard/spend.phtml"}}
    </div>
</div>'
);
Mage::getModel('cms/page')->setData($welcomecmsPageData)->save();
