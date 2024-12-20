<?php
namespace Bentonow\Bento\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class JobActions extends Column
{
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['job_id'])) {
                    $item[$this->getData('name')] = [
                        'requeue' => [
                            'href' => $this->urlBuilder->getUrl(
                                'bentonow_bento/job/requeue',
                                ['job_id' => $item['job_id']]
                            ),
                            'label' => __('Requeue')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                'bentonow_bento/job/delete',
                                ['job_id' => $item['job_id']]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Job'),
                                'message' => __('Are you sure you want to delete this job?')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}