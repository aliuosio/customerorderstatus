<?php

namespace AliuOsio\CustomerOrderstatus\Cron;

use AliuOsio\CustomerOrderstatus\Helper\Data as Helper;
use AliuOsio\CustomerOrderstatus\Model\Order;
use Psr\Log\LoggerInterface;

class Cancel
{

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var Helper */
    private $helper;

    /** @var Order */
    private $order;

    public function __construct(
        LoggerInterface $logger,
        Order $order,
        Helper $helper
    )
    {
        $this->_logger = $logger;
        $this->helper = $helper;
        $this->order = $order;
    }

    public function execute()
    {
        if ($this->helper->isEnabled()) {
            foreach ($this->order->cancelOrders($this->helper->getPeriodForCollection()) as $orderId) {
                $this->_logger->info("The OrderId: {$orderId} was cancelled");
            }
        }

        return $this;
    }

}
