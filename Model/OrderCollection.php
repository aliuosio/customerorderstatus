<?php

namespace AliuOsio\CustomerOrderstatus\Model;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class OrderCollection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init('AliuOsio\CustomerOrderstatus\Model\Order', 'Magento\Sales\Model\ResourceModel\Order');
    }

    public function filterOrdersToCancel($period)
    {
        $this->getSelect()
            ->join(['customer' => $this->getTable('customer_entity')], 'main_table.customer_id = customer.entity_id', [])
            ->where('main_table.state = ?', \Magento\Sales\Model\Order::STATE_HOLDED)
            ->where('customer.created_at < ?', $period)
            ->where('customer.confirmation != ?', '');

        return $this;
    }

}
