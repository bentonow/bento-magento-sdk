<?php

namespace Bentonow\Bento\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Bentonow\Bento\Model\JobFactory;
use Magento\Framework\Serialize\Serializer\Json;

class OrderShipped implements ObserverInterface
{
    protected $jobFactory;
    protected $jsonSerializer;

    public function __construct(
        JobFactory $jobFactory,
        Json       $jsonSerializer
    )
    {
        $this->jobFactory = $jobFactory;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function execute(Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();

        $jobData = [
            'job_type' => 'order_shipped',
            'status' => 'pending',
            'data' => $this->jsonSerializer->serialize([
                'events' => [
                    [
                        'email' => $order->getCustomerEmail(),
                        'type' => '$order_shipped',
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
                            'shipment' => [
                                'id' => $shipment->getIncrementId(),
                                'tracking' => $this->getTrackingInfo($shipment)
                            ]
                        ]
                    ]
                ]
            ])
        ];

        $job = $this->jobFactory->create();
        $job->setData($jobData)->save();
    }

    private function getTrackingInfo($shipment)
    {
        $trackingInfo = [];
        foreach ($shipment->getAllTracks() as $track) {
            $trackingInfo[] = [
                'carrier' => $track->getTitle(),
                'number' => $track->getTrackNumber()
            ];
        }
        return $trackingInfo;
    }
}