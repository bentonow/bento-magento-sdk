<?php
namespace Bentonow\Bento\Model\ResourceModel\Job\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _construct()
    {
        $this->_init('Magento\Framework\View\Element\UiComponent\DataProvider\Document', 'Bentonow\Bento\Model\ResourceModel\Job');
        $this->setMainTable('bentonow_bento_job');
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFilterToMap('job_id', 'main_table.job_id');
        return $this;
    }
}