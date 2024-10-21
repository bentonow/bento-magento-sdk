<?php
namespace Bentonow\Bento\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Bentonow\Bento\Model\JobFactory;
use Magento\Framework\Serialize\Serializer\Json;

class OrderClosed implements ObserverInterface
{
    protected $jobFactory;
    protected $jsonSerializer;

    public function __construct(
        JobFactory $jobFactory,
        Json $jsonSerializer
    ) {
        $this->jobFactory = $jobFactory;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        // Check if the order status has changed to 'closed'
        if ($order->getOrigData('status') !== $order->getData('status') && $order->getData('status') === 'complete') {
            $jobData = [
                'job_type' => 'order_closed',
                'status' => 'pending',
                'data' => $this->jsonSerializer->serialize([
                    'events' => [
                        [
                            'email' => $order->getCustomerEmail(),
                            'type' => '$order_closed',
                            'fields' => [
                                'first_name' => $order->getCustomerFirstname(),
                                'last_name' => $order->getCustomerLastname(),
                            ],
                            'details' => [
                                'unique' => [
                                    'key' => $order->getIncrementId()
                                ],
                                'value' => [
                                    'currency' => $order->getOrderCurrencyCode(),
                                    'amount' => (int)($order->getGrandTotal() * 100)
                                ],
                                'closed_at' => $order->getUpdatedAt()
                            ]
                        ]
                    ]
                ])
            ];

            $job = $this->jobFactory->create();
            $job->setData($jobData)->save();
        }
    }
}