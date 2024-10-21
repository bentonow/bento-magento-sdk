<?php
namespace Bentonow\Bento\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Job extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('bentonow_bento_job', 'job_id');
    }
}