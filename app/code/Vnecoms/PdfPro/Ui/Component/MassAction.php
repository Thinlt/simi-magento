<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 17/01/2017
 * Time: 01:44
 */

namespace Vnecoms\PdfPro\Ui\Component;


class MassAction extends \Magento\Ui\Component\MassAction
{
    /** @var array  */
    protected $removeType = ['pdfdocs_order','pdfinvoices_order','pdfshipments_order','pdfcreditmemos_order'];

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getConfiguration();

        foreach ($this->getChildComponents() as $actionComponent) {
            $config['actions'][] = $actionComponent->getConfiguration();
        };

        $origConfig = $this->getConfiguration();
        if ($origConfig !== $config) {
            $config = array_replace_recursive($config, $origConfig);
        }

        $newConfigActions = [];
        foreach ($config['actions'] as $configItem) {
            if(
							!isset($configItem['type']) ||
							in_array($configItem['type'], $this->removeType)
						) {
                continue;
            }

            $newConfigActions[] = $configItem;
        }

        $config['actions'] = $newConfigActions;

        $this->setData('config', $config);
        $this->components = [];

        parent::prepare();
    }
}