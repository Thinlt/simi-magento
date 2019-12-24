<?php

namespace Simi\Simicustompayment\Model\Api;


class Payfortredirects extends \Simi\Simiconnector\Model\Api\Apiabstract {

	protected $response;

	public function __construct(
		\Magento\Framework\App\Response\Http $response
	)
	{
		$this->response = $response;
	}

	public function setBuilderQuery() {
		// TODO: Implement setBuilderQuery() method.
	}

	public function index() {
		$this->response->setRedirect('payfortfort/payment/redirect');
	}
}