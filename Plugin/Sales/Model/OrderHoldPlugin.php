<?php

namespace AliuOsio\CustomerOrderstatus\Plugin\Sales\Model;

use AliuOsio\CustomerOrderstatus\Helper\Data as Helper;
use AliuOsio\CustomerOrderstatus\Model\OrderCollectionFactory;
use AliuOsio\CustomerOrderstatus\Plugin\CustomerOrderstatusAbstract;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Zend\Barcode\Renderer\Exception\InvalidArgumentException;
use Magento\Customer\Model\Backend\Customer;

class OrderHoldPlugin extends CustomerOrderstatusAbstract
{

    /** @var CustomerFactory */
    private $customerFactory;

    /** @var Customer\Interceptor */
    private $customer;

    /** @var string */
    private $paymentMethod;

    public function __construct(
        Helper $helper,
        OrderCollectionFactory $orderCollectionFactory,
        CustomerFactory $customerFactory
    )
    {
        $this->customerFactory = $customerFactory;
        $this->helper = $helper;
        parent::__construct($helper, $orderCollectionFactory);
    }

    /**
     * @param Order\Interceptor $subject
     * @param string $result
     * @return string
     * @throws LocalizedException
     */
    public function beforeSetState(Order\Interceptor $subject, string $result): string
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        $this->setCustomer($subject);
        $this->setPaymentMethod($subject);
        $this->checkIfHasToCancelOrder();

        if ($this->hasToHoldOrder($result)) {
            $result = Order::STATE_HOLDED;
        }

        return $result;
    }

    /**
     * @throws LocalizedException
     */
    private function checkIfHasToCancelOrder()
    {
        if ($this->hasToCancelOrder()) {
            throw new LocalizedException(
                __('User has not confirmed E-Mail Address in the %1 Days Period. This Order can therefore not be saved',
                    $this->helper->getCancellationPeriod())
            );
        }
    }

    private function hasToHoldOrder(string $result): bool
    {
        return !$this->isCustomerEmailConfirmed() && in_array($result, Helper::STATE_TO_CHECK);
    }

    /**
     * @param Order\Interceptor $order
     */
    private function setPaymentMethod(Order\Interceptor $order)
    {
        $this->paymentMethod = $order->getPayment()->getMethod();
    }

    private function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * @param $customer
     */
    protected function setCustomer($customer)
    {
        if (is_object($customer)) {
            $this->customer = $this->customerFactory
                ->create()
                ->load($customer->getCustomerId());
        } else {
            throw new InvalidArgumentException('Param in ' . __METHOD__ . 'is not an object');
        }
    }

    protected function getCustomer(): Customer\Interceptor
    {
        return $this->customer;
    }

}
