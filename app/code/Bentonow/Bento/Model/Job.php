<?php
namespace Bentonow\Bento\Model;

use Magento\Framework\Model\AbstractModel;

class Job extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Bentonow\Bento\Model\ResourceModel\Job::class);
    }
}