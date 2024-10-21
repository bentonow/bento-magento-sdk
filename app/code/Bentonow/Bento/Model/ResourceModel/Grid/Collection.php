<?php
namespace Bentonow\Bento\Model\ResourceModel\Job\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _initSelect()
    {
        $this->addFilterToMap('job_id', 'main_table.job_id');
        parent::_initSelect();
    }
}