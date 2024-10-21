<?php
namespace Bentonow\Bento\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Bentonow\Bento\Model\JobFactory;
use Magento\Framework\Serialize\Serializer\Json;

class OrderHeld implements ObserverInterface
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

        $jobData = [
            'job_type' => 'order_held',
            'status' => 'pending',
            'data' => $this->jsonSerializer->serialize([
                'events' => [
                    [
                        'email' => $order->getCustomerEmail(),
                        'type' => '$order_held',
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
                            'held_at' => $order->getUpdatedAt()
                        ]
                    ]
                ]
            ])
        ];

        $job = $this->jobFactory->create();
        $job->setData($jobData)->save();
    }
}