<?php

namespace AliuOsio\CustomerOrderstatus\Plugin\Customer\Model;

use AliuOsio\CustomerOrderstatus\Helper\Data as helper;
use AliuOsio\CustomerOrderstatus\Plugin\CustomerOrderstatusAbstract;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement;
use AliuOsio\CustomerOrderstatus\Model\OrderCollectionFactory;

class AccountManagementPlugin extends CustomerOrderstatusAbstract
{

    /** @var OrderCollectionFactory */
    private $orderCollectionFactory;

    /** @var CustomerInterface */
    private $customer;

    public function __construct(
        Helper $helper,
        OrderCollectionFactory $orderCollectionFactory
    )
    {
        $this->helper = $helper;
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($helper, $orderCollectionFactory);
    }

    public function afterActivate(
        AccountManagement\Interceptor $subject,
        CustomerInterface $result
    ): CustomerInterface
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        $this->setCustomer($result);
        $this->setOrdersToUnOnHold();

        return $result;
    }

    /**
     * @todo refactor if perfomance issues come up
     */
    private function setOrdersToUnOnHold()
    {
        foreach ($this->getOrdersOnHold($this->getCustomer()->getId()) as $order) {
            $order->setData('state', $order->getStatus())
                ->save();
        }
    }

    protected function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    protected function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

}
