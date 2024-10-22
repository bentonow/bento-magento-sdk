<?php
namespace Bentonow\Bento\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Bentonow\Bento\Model\JobFactory;
use Magento\Framework\Serialize\Serializer\Json;

class OrderPlace implements ObserverInterface
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
            'job_type' => 'order_created',
            'status' => 'pending',
            'data' => $this->jsonSerializer->serialize([
                'events' => [
                    [
                        'email' => $order->getCustomerEmail(),
                        'type' => '$OrderCreated',
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
                                'amount' => (int)($order->getGrandTotal() * 100) // Convert to cents
                            ],
                            'cart' => [
                                'items' => $this->getOrderItems($order),
                            ]
                        ]
                    ]
                ]
            ])
        ];

        $job = $this->jobFactory->create();
        $job->setData($jobData)->save();
    }

    private function getOrderItems($order)
    {
        $items = [];
        foreach ($order->getAllItems() as $item) {
            $items[] = [
                'product_sku' => $item->getSku(),
                'product_name' => $item->getName(),
                'quantity' => (int)$item->getQtyOrdered()
            ];
        }
        return $items;
    }
}