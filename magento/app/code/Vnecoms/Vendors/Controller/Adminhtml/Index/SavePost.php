<?php
namespace Vnecoms\Vendors\Controller\Adminhtml\Index;

use Vnecoms\Vendors\Controller\Adminhtml\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Backend\Model\UrlFactory;
use Magento\Customer\Api\AccountManagementInterface;

class SavePost extends Action
{

    /** @var AccountManagementInterface */
    protected $accountManagement;

    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $vendorFactory;

    /** @var CustomerExtractor */
    protected $customerExtractor;


    /** @var \Magento\Backend\Model\UrlFactory */
    protected $urlModel;

    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;

    /**
     * @var \Vnecoms\Credit\Model\Credit
     */
    protected $creditAccount;

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_Vendors::vendors_sellers');
    }


    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        CustomerExtractor $customerExtractor,
        UrlFactory $urlFactory,
        AccountManagementInterface $accountManagement,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory
    ) {
        $this->vendorFactory = $vendorFactory;
        $this->customerExtractor = $customerExtractor;
        $this->urlModel = $urlFactory->create();
        $this->accountManagement = $accountManagement;
        $this->_vendorHelper = $vendorHelper;
        $this->creditAccount = $creditAccountFactory->create();
        parent::__construct($context, $coreRegistry, $dateFilter);
    }


    /**
     * @return void
     */
    public function execute()
    {
        $originalRequestData = $this->getRequest()->getPostValue();
        if ($originalRequestData) {
            try {
                // create and save customer
                $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);
                $customer->setAddresses([]);

                $customer->setWebsiteId($originalRequestData["website_id"]);

                $password = $this->getRequest()->getParam('password');
                $confirmation = $this->getRequest()->getParam('password_confirmation');
                $redirectUrl = $this->urlModel->getUrl('*/*/new');

                $this->checkPasswordConfirmation($password, $confirmation);

                $customer = $this->accountManagement
                    ->createAccount($customer, $password, $redirectUrl);
                
                $this->creditAccount->loadByCustomerId($customer->getId());
                // After save
                $this->_eventManager->dispatch(
                    'adminhtml_customer_save_after',
                    ['customer' => $customer, 'request' => $this->getRequest()]
                );
                // optional fields might be set in request for future processing by observers in other modules
                $vendorData = $this->getRequest()->getParam('vendor_data');
                $request = $this->getRequest();

                $vendor = $this->vendorFactory->create();

                $vendor->addData($vendorData);

                $vendor->setCustomer($customer);
                $vendor->setWebsiteId($customer->getWebsiteId());

                $this->_eventManager->dispatch(
                    'adminhtml_vendor_prepare_save',
                    ['vendor' => $vendor, 'request' => $request]
                );

                // Save vendor
                $vendor->save();

                if ($this->_vendorHelper->isRequiredVendorApproval()) {
                    $vendor->sendNewAccountEmail("registered");
                } else {
                    $vendor->sendNewAccountEmail("active");
                }


                $this->_eventManager->dispatch(
                    'adminhtml_vendor_save_after',
                    ['vendor' => $vendor, 'request' => $request]
                );
                // Done Saving customer, finish save action
                $this->_coreRegistry->register('current_vendor_id', $vendor->getId());
                $this->messageManager->addSuccess(__('You saved the vendor.'));


                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('vendors/*/edit', ['id' => $vendor->getId()]);
                    return;
                }
                $this->_getSession()->unsCustomerFormData();
                $this->_redirect('vendors/index/');

                return;
            } catch (\Magento\Eav\Model\Entity\Attribute\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setCustomerFormData($originalRequestData);
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('vendors/*/new');
                return;
            } catch (\Exception $e) {
                $this->_getSession()->setCustomerFormData($originalRequestData);

                $this->messageManager->addError(
                    __('Something went wrong while saving the seller data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('vendors/*/new');
                return;
            }
        }
        $this->_redirect('vendors/*/');
    }


    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password
     * @param string $confirmation
     * @return void
     * @throws InputException
     */
    protected function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }


}
