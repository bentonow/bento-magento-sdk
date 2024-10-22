<?php
namespace Bentonow\Bento\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Bentonow\Bento\Model\JobFactory;
use Magento\Framework\Serialize\Serializer\Json;

class OrderRefunded implements ObserverInterface
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
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        $jobData = [
            'job_type' => 'order_refunded',
            'status' => 'pending',
            'data' => $this->jsonSerializer->serialize([
                'events' => [
                    [
                        'email' => $order->getCustomerEmail(),
                        'type' => '$RefundCreated',
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
                                'amount' => (int)($creditmemo->getGrandTotal() * 100)
                            ],
                            'refund' => [
                                'id' => $creditmemo->getIncrementId(),
                                'reason' => $creditmemo->getCustomerNote()
                            ]
                        ]
                    ]
                ]
            ])
        ];

        $job = $this->jobFactory->create();
        $job->setData($jobData)->save();
    }
}