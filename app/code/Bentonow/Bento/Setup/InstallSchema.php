<?php
namespace Bentonow\Bento\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('bentonow_bento_job'))
            ->addColumn(
                'job_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Job ID'
            )
            ->addColumn(
                'job_type',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Job Type'
            )
            ->addColumn(
                'status',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'data',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Job Data'
            )
            ->addColumn(
                'error_message',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'Error Message'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )
            ->setComment('Bento Job Table');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}