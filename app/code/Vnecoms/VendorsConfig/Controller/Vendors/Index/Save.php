<?php
namespace Vnecoms\VendorsConfig\Controller\Vendors\Index;

use Vnecoms\Vendors\App\Action\Context;
use Magento\Config\Model\Config\Structure as ConfigStructure;
use Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker;
use \Magento\Framework\Exception\LocalizedException;

class Save extends \Vnecoms\VendorsConfig\Controller\Vendors\AbstractConfig
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsConfig::configuration';
    
    /**
     * Backend Config Model Factory
     *
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_configHelper;


    /**
     * @param Context $context
     * @param ConfigStructure $configStructure
     * @param ConfigSectionChecker $sectionChecker
     * @param \Vnecoms\VendorsConfig\Helper\Data $configHelper
     */
    public function __construct(
        Context $context,
        ConfigStructure $configStructure,
        ConfigSectionChecker $sectionChecker,
        \Vnecoms\VendorsConfig\Helper\Data $configHelper
    ) {
        parent::__construct($context, $configStructure, $sectionChecker);
        $this->_configHelper = $configHelper;
    }

    /**
     * Get groups for save
     *
     * @return array|null
     */
    protected function getGroupsDataForSave()
    {
        $request = $this->getRequest();
        $groupsData = $request->getPost('groups');
        $fileArrs = $request->getFiles('groups');

        if ($fileArrs && is_array($fileArrs)) {
            foreach ($fileArrs as $grName => $groupData) {
                $data = $this->processNestedGroupsData($groupData);
                if (!empty($data)) {
                    $groupsData[$grName] = !empty($groupsData[$grName])?
                        array_merge_recursive((array)$groupsData[$grName], $data):
                        $data;
                }
            }
        }
        return $groupsData;
    }

    /**
     * @param mixed $group
     * @return array
     */
    protected function processNestedGroupsData($groupData)
    {
        $data = [];

        if (isset($groupData['fields']) && is_array($groupData['fields'])) {
            foreach ($groupData['fields'] as $fieldName => $field) {
                if (!empty($field['value'])) {
                    $data['fields'][$fieldName] = ['value' => $field['value']];
                }
            }
        }

        if (isset($groupData['groups']) && is_array($groupData['groups'])) {
            foreach ($groupData['groups'] as $grName => $grData) {
                $nestedGroup = $this->processNestedGroupsData($grData);
                if (!empty($nestedGroup)) {
                    $data['groups'][$grName] = $nestedGroup;
                }
            }
        }

        return $data;
    }


    /**
     * Save configuration
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $section = $this->getRequest()->getParam('section');
            $storeId = (int) $this->getRequest()->getParam('store');
            $this->_configHelper->saveConfig(
                $this->_session->getVendor()->getId(),
                $section, 
                $this->getGroupsDataForSave(),
                $storeId
            );

            $this->messageManager->addSuccess(__('You saved the configuration.'));
        } catch (LocalizedException $e) {
            $messages = explode("\n", $e->getMessage());
            foreach ($messages as $message) {
                $this->messageManager->addError($message);
            }
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong while saving this configuration:') . ' ' . $e->getMessage()
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath(
            'config/index/edit',
            ['_current' => ['section', 'store'], '_nosid' => true]
        );
    }
}
