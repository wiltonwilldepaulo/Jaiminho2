<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InstallmentSale extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('installment_sale', [
            'id' => false,
            'primary_key' => ['id'],
            'comment' => 'Tabela responsável por guardar dados do parcelamento da venda.'
        ]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])
            ->addColumn('id_venda', 'biginteger', ['null' => true])
            ->addColumn('id_pagamento', 'biginteger', ['null' => true])
            ->addColumn('parcela', 'integer', ['null' => true, 'default' => 0])
            ->addColumn('intervalo', 'integer', ['null' => true, 'default' => 0])
            ->addColumn('titulo', 'string', ['null' => true, 'default' => ''])
            ->addColumn('valor_total_venda', 'decimal', ['null' => true, 'default' => 0, 'precision' => 18, 'scale' => 4])
            ->addColumn('valor_total_meio_pagamento', 'decimal', ['null' => true, 'default' => 0, 'precision' => 18, 'scale' => 4])
            ->addColumn('valor_parcela', 'decimal', ['null' => true, 'default' => 0, 'precision' => 18, 'scale' => 4])
            ->addColumn('data_vencimento', 'datetime', ['null' => true])
            ->addColumn('data_cadastro', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('data_atualizacao', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('id_venda', 'sale', 'id', ['delete' => 'CASCADE', 'update' => 'NO ACTION'])
            ->addForeignKey('id_pagamento', 'payment_terms', 'id', ['delete' => 'CASCADE', 'update' => 'NO ACTION'])
            ->create();
    }
}
