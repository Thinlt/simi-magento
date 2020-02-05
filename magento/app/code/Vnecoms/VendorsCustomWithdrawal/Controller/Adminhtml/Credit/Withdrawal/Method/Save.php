<?php
namespace Vnecoms\VendorsCustomWithdrawal\Controller\Adminhtml\Credit\Withdrawal\Method;

use Vnecoms\Vendors\Controller\Adminhtml\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

class Save extends Action
{
    /**
     * @var \Vnecoms\VendorsCustomWithdrawal\Model\MethodFactory
     */
    protected $methodFactory;
    
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsCustomWithdrawal::withdrawal_methods');
    }
    
    
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     * @param \Vnecoms\VendorsCustomWithdrawal\Model\MethodFactory $methodFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        \Vnecoms\VendorsCustomWithdrawal\Model\MethodFactory $methodFactory
    ) {
        $this->methodFactory = $methodFactory;
        
        parent::__construct($context, $coreRegistry, $dateFilter);
    }
    
    
    /**
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
             try {
                $method = $this->methodFactory->create();
                $data = $this->getRequest()->getParams();
                $id = $this->getRequest()->getParam('method_id');
                if($id){
                    $method->load($id);
                }
                unset($data['method_id']);
                $method->addData($data);
                $method->save();
                $this->messageManager->addSuccess(__('You saved the method.'));
                
                
                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('vendors/*/edit', ['id' => $method->getId()]);
                }
                
                return $this->_redirect('vendors/*/');
            } catch (\Magento\Eav\Model\Entity\Attribute\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                return $this->_redirect('vendors/*/edit', ['id' => $this->getRequest()->getParam('method_id')]);
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the seller data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                return $this->_redirect('vendors/*/edit', ['id' => $this->getRequest()->getParam('method_id')]);
            }
        }
        $this->_redirect('vendors/*/');
    }
}
