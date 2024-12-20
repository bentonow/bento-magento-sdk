<?php
namespace Bentonow\Bento\Cron;

use Bentonow\Bento\Model\ResourceModel\Job\CollectionFactory;
use Bentonow\Bento\Helper\Data;
use Magento\Framework\HTTP\Client\Curl;

class ProcessQueue
{
    protected $jobCollectionFactory;
    protected $helper;
    protected $curl;

    public function __construct(
        CollectionFactory $jobCollectionFactory,
        Data $helper,
        Curl $curl
    ) {
        $this->jobCollectionFactory = $jobCollectionFactory;
        $this->helper = $helper;
        $this->curl = $curl;
    }

    public function execute()
    {
        $collection = $this->jobCollectionFactory->create();
        $collection->addFieldToFilter('status', 'pending');

        foreach ($collection as $job) {
            $this->processJob($job);
        }
    }

    protected function processJob($job)
    {
        $data = json_decode($job->getData('data'), true);
        $endpoint = $job->getData('job_type') === 'subscriber' ? 'batch/subscribers' : 'batch/events';

        $url = $this->helper->getBaseUrl() . $endpoint . '?site_uuid=' . $this->helper->getSiteUuid();

        $this->curl->setHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->helper->getUsername() . ':' . $this->helper->getPassword()),
            'User-Agent' => $this->helper->getUserAgent()
        ]);

        $this->curl->post($url, json_encode($data));

        $statusCode = $this->curl->getStatus();
        $response = $this->curl->getBody();

        $job->setHttpStatusCode($statusCode);

        if ($statusCode >= 200 && $statusCode < 300) {
            $job->setStatus('completed');
        } else {
            $job->setStatus('error');
            $job->setErrorMessage($response);
        }

        $job->save();
    }
}