<?php

namespace AliuOsio\CustomerOrderstatus\Console\Command;

use AliuOsio\CustomerOrderstatus\Helper\Data as Helper;
use AliuOsio\CustomerOrderstatus\Model\Order;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CancelOnholdCommand extends Command
{

    /** @var Helper */
    private $helper;

    /** @var string */
    const COMMAND_NAME = 'lw:cancel:onhold';

    /** @var Order */
    private $order;

    public function __construct(Helper $helper, Order $order, string $name = null)
    {
        parent::__construct($name);
        $this->helper = $helper;
        $this->order = $order;
    }

    protected function configure(): void
    {
        $this->setName(CancelOnholdCommand::COMMAND_NAME)
            ->setDescription(__('Cancel Orders onHold for Customers with a non confirmed E-Mail Address'));

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): Command
    {
        if (!$this->helper->isEnabled()) {
            $output->writeln(CancelOnholdCommand::COMMAND_NAME . ' disabled under configuraion aliuosio tab');
        } else {
            foreach ($this->order->cancelOrders($this->helper->getPeriodForCollection()) as $orderId) {
                $output->writeln("The OrderId: {$orderId} was cancelled");
            }
        }

        return $this;
    }

}
