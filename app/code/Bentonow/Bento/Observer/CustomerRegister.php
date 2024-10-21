<?php
namespace Bentonow\Bento\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Bentonow\Bento\Model\JobFactory;
use Magento\Framework\Serialize\Serializer\Json;

class CustomerRegister implements ObserverInterface
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
        $customer = $observer->getEvent()->getCustomer();

        $jobData = [
            'job_type' => 'subscriber',
            'status' => 'pending',
            'data' => $this->jsonSerializer->serialize([
                'subscribers' => [
                    [
                        'email' => $customer->getEmail(),
                        'first_name' => $customer->getFirstname(),
                        'last_name' => $customer->getLastname(),
                        'tags' => 'lead,mql',
                    ]
                ]
            ])
        ];

        $job = $this->jobFactory->create();
        $job->setData($jobData)->save();
    }
}