<?php
namespace Bentonow\Bento\Model\ResourceModel\Job;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Bentonow\Bento\Model\Job::class,
            \Bentonow\Bento\Model\ResourceModel\Job::class
        );
    }
}