<?php

use Migrations\AbstractMigration;

class AddAttachmentsToEmailQueue extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * @return void
     */
    public function change()
    {
        $table = $this->table('email_queue');
        $table->addColumn(
            'status_id',
            'string',
            [
            'default' => null,
            'limit' => 100,
            'null' => false,
            ]
        )->addColumn(
            'status',
            'string',
            [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ]
        )->addColumn(
            'status_message',
            'text',
            [
                'default' => null,
                'limit' => null,
                'null' => true,
            ]
        );
        $table->update();
    }
}

