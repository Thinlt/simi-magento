<?php
/**
 * Created by PhpStorm.
 * User: TUNA
 * Date: 4/4/2019
 * Time: 10:38 PM
 */

namespace Vnecoms\PdfPro\Plugin;

use Vnecoms\PdfPro\Model\EmailEventDispatcher;

class TransportFactory
{
    /**
     * @var EmailEventDispatcher
     */
    private $emailEventDispatcher;

    public function __construct(
        EmailEventDispatcher $emailEventDispatcher
    ) {
        $this->emailEventDispatcher = $emailEventDispatcher;
    }

    public function aroundCreate(
        \Magento\Framework\Mail\TransportInterfaceFactory $subject,
        \Closure $proceed,
        array $data = []
    ) {
        if (isset($data['message'])) {
            $this->emailEventDispatcher->dispatch($data['message']);
        }
        return $proceed($data);
    }
}
