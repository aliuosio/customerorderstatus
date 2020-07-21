<?php

namespace AliuOsio\CustomerOrderstatus\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    /** @var string */
    const XML_PATH_ENABLED = 'aliuosio_customerorderstatus/general/enabled';

    /** @var string */
    const XML_PATH_DEBUG = 'aliuosio_customerorderstatus/general/debug';

    /** @var string */
    const XML_PATH_CANCEL_PERIOD = 'aliuosio_customerorderstatus/general/order_cancellation_period_days';

    /** @var array */
    const STATE_TO_CHECK = [Order::STATE_NEW, Order::STATE_PROCESSING];

    public function __construct(Context $context, TimezoneInterface $timezone)
    {
        parent::__construct($context, $timezone);
    }

    public function isEnabled(): bool
    {
        return (bool)$this->isModuleEnabled(Data::XML_PATH_ENABLED);
    }

    public function getCancellationPeriod(): int
    {
        return (int)$this->getConfigValue(Data::XML_PATH_CANCEL_PERIOD);
    }

    /**
     * @inheritDoc
     */
    public function getPeriodForCollection()
    {
        return $this->getTimeStampForPeriod(
            '-',
            $this->getCancellationPeriod()
        );
    }

    /**
     * @param string $path
     * @param string $scope
     * @return bool
     */
    public function isModuleEnabled(string $path, $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return (bool)$this->getConfigValue($path, $scope);
    }

    public function getConfigValue(string $path, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($path, $scope);
    }

}
