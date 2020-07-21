<?php

namespace AliuOsio\CustomerOrderstatus\Model;

use \Magento\Sales\Model\Order as MagentoOrder;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Api\OrderManagementInterface;

class Order extends AbstractModel
{

    /** @var OrderCollectionFactory */
    private $orderCollectionFactory;

    /** @var OrderManagementInterface */
    private $orderManagement;

    /** @var MagentoOrder */
    private $order;

    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        OrderManagementInterface $orderManagement,
        MagentoOrder $order
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderManagement = $orderManagement;
        $this->order = $order;
    }

    private function getOrdersCollection(string $period): OrderCollection
    {
        return $this->orderCollectionFactory->create()
            ->filterOrdersToCancel($period);
    }

    public function cancelOrders(string $period): array
    {
        $result = [];
        /** @var  $order */
        foreach ($this->getOrdersCollection($period) as $order) {
            /*
            if ($this->orderManagement->cancel($order->getEntityId())) {
             $result[] = $order->getEntityId();
            }
            */
            if ($this->order->load($order->getEntityId())
                ->setState(MagentoOrder::STATE_CANCELED)
                ->setStatus(MagentoOrder::STATE_CANCELED)
                ->save()) {
                $result[] = $order->getEntityId();
            }
        }

        return $result;
    }

}
