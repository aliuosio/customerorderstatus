<?php

namespace AliuOsio\CustomerOrderstatus\Plugin;

use DateTime;
use Exception;
use AliuOsio\CustomerOrderstatus\Helper\Data as Helper;
use Magento\Sales\Model\Order;
use AliuOsio\CustomerOrderstatus\Model\OrderCollection as OrderCollection;
use AliuOsio\CustomerOrderstatus\Model\OrderCollectionFactory;

abstract class CustomerOrderstatusAbstract
{

    /** @var Helper */
    protected $helper;

    /** @var OrderCollectionFactory */
    private $ordersCollectionFactory;

    abstract protected function setCustomer($object);

    abstract protected function getCustomer();

    public function __construct(Helper $helper, OrderCollectionFactory $ordersCollectionFactory)
    {
        $this->helper = $helper;
        $this->ordersCollectionFactory = $ordersCollectionFactory;
    }

    protected function isCustomerEmailConfirmed(): bool
    {
        return !(bool)$this->getCustomer()->getConfirmation();
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function hasToCancelOrder(): bool
    {
        return $this->getPeriodSinceRegistration() > $this->helper->getCancellationPeriod();
    }

    /**
     * @return int
     * @throws Exception
     */
    protected function getPeriodSinceRegistration(): int
    {
        return $this->helper->getPeriod(
            $this->helper->getCurrentDateTime(),
            $this->getCustomerCreateAtAsDateTime()
        );
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    protected function getCustomerCreateAtAsDateTime()
    {
        return $this->helper->getDateTime($this->getCustomer()->getCreatedAt());
    }

    protected function getOrdersOnHold($customerId): OrderCollection
    {
        return $this->ordersCollectionFactory
            ->create([$customerId])
            ->addAttributeToFilter('state', Order::STATE_HOLDED)
            ->load();
    }


}
