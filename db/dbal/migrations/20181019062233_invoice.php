<?php

use Phinx\Migration\AbstractMigration;

class Invoice extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('invoices')
            ->addColumn('header', 'string')
            ->addColumn('invoice_date', 'date')
            ->addColumn('invoice_number', 'string', ['limit' => 100])
            ->addColumn('paid_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('recipient_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('subtotal', 'text')
            ->addColumn('tax_rate', 'float')
            ->addColumn('taxes', 'text')
            ->addColumn('total', 'text')
            ->create();
    }
}
