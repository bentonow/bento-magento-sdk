<?php
namespace Bentonow\Bento\Controller\Adminhtml\Job;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Bentonow\Bento\Model\JobFactory;

class Requeue extends Action
{
    protected $jobFactory;

    public function __construct(
        Context $context,
        JobFactory $jobFactory
    ) {
        parent::__construct($context);
        $this->jobFactory = $jobFactory;
    }

    public function execute()
    {
        $jobId = $this->getRequest()->getParam('job_id');
        try {
            $job = $this->jobFactory->create()->load($jobId);
            if ($job->getId()) {
                $job->setStatus('pending');
                $job->setHttpStatusCode(null);
                $job->save();
                $this->messageManager->addSuccessMessage(__('Job has been requeued.'));
            } else {
                $this->messageManager->addErrorMessage(__('Job not found.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bentonow_Bento::job');
    }
}