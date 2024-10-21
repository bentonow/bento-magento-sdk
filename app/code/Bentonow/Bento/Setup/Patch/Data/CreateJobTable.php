<?php
namespace Bentonow\Bento\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class CreateJobTable implements DataPatchInterface
{
    private $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $tableName = $this->moduleDataSetup->getTable('bentonow_bento_job');

        if (!$this->moduleDataSetup->getConnection()->isTableExists($tableName)) {
            $table = $this->moduleDataSetup->getConnection()
                ->newTable($tableName)
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

            $this->moduleDataSetup->getConnection()->createTable($table);
        }

        $this->moduleDataSetup->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}