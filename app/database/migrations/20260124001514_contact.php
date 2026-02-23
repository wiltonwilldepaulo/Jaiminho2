<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Contact extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('contact', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])
            ->addColumn('id_usuario', 'biginteger', ['null' => true])
            ->addColumn('id_cliente', 'biginteger', ['null' => true])
            ->addColumn('id_fornecedor', 'biginteger', ['null' => true])
            ->addColumn('tipo', 'text', ['null' => true])
            ->addColumn('endereco_contato', 'text', ['null' => true])
            ->addForeignKey('id_usuario', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO ACTION'])
            ->addForeignKey('id_cliente', 'customer', 'id', ['delete' => 'CASCADE', 'update' => 'NO ACTION'])
            ->addForeignKey('id_fornecedor', 'supplier', 'id', ['delete' => 'CASCADE', 'update' => 'NO ACTION'])
            ->create();
    }
}
