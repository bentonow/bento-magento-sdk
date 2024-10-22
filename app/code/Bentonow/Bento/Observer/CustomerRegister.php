<?php
namespace Bentonow\Bento\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Bentonow\Bento\Model\JobFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CustomerRegister implements ObserverInterface
{
    protected $jobFactory;
    protected $jsonSerializer;
    protected $scopeConfig;

    public function __construct(
        JobFactory $jobFactory,
        Json $jsonSerializer,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->jobFactory = $jobFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        if (!$this->scopeConfig->getValue('bentonow_bento/general/enable_customer_register')) {
            return;
        }

        $customer = $observer->getEvent()->getCustomer();
        $tags = $this->scopeConfig->getValue('bentonow_bento/general/customer_tags');

        $jobData = [
            'job_type' => 'subscriber',
            'status' => 'pending',
            'data' => $this->jsonSerializer->serialize([
                'subscribers' => [
                    [
                        'email' => $customer->getEmail(),
                        'first_name' => $customer->getFirstname(),
                        'last_name' => $customer->getLastname(),
                        'tags' => $tags,
                    ]
                ]
            ])
        ];

        $job = $this->jobFactory->create();
        $job->setData($jobData)->save();
    }
}