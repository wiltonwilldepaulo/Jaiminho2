<?php

namespace app\controller;

use app\database\builder\DeleteQuery;
use app\database\builder\InsertQuery;
use app\database\builder\SelectQuery;
use app\database\builder\UpdateQuery;

class Sale extends Base
{
    public function cadastro($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Página inicial',
            'acao' => 'c'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('sale'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function lista($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Página inicial'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('listsale'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function insert($request, $response)
    {
        #captura os dados do formulário
        $form = $request->getParsedBody();
        #Captura o id do produto
        $id_produto = $form['pesquisa'];
        #Verificar se o id do produto esta vasio ou nulo
        if (empty($id_produto) or is_null($id_produto)) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: O ID do produto é obrigatório!',
                'id' => 0
            ], 403);
        }
        #seleciona o id do cliente CONSUMIDOR FINAL para inserir a venda
        $customer = SelectQuery::select('id')
            ->from('customer')
            ->order('id', 'asc')
            ->limit(1)
            ->fetch();
        #Verificar se o cliente não foi encontrado
        if (!$customer) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: Nenhum cliente encontrado!',
                'id' => 0
            ], 403);
        }
        #seleciona o id do cliente CONSUMIDOR FINAL para inserir a venda
        $id_customer = $customer['id'];
        $FieldAndValue = [
            'id_cliente' => $id_customer,
            'total_bruto' => 0,
            'total_liquido' => 0,
            'desconto' => 0,
            'acrescimo' => 0,
            'observacao' => ''
        ];
        try {
            #Tenta inserir a venda no banco de dados e captura o resultado da inserção
            $IsInserted = InsertQuery::table('sale')->save($FieldAndValue);
            #Verificar se a inserção falhou
            if (!$IsInserted) {
                return $this->SendJson(
                    $response,
                    [
                        'status' => false,
                        'msg' => 'Restrição: Falha ao inserir a venda!',
                        'id' => 0
                    ],
                    403
                );
            }
            #Seleciona o id da venda inserida mais recente para retornar na resposta
            $sale = SelectQuery::select('id')
                ->from('sale')
                ->order('id', 'desc')
                ->limit(1)
                ->fetch();
            #Verificar se a venda não foi encontrada
            if (!$sale) {
                return $this->SendJson($response, [
                    'status' => false,
                    'msg' => 'Restrição: Nenhuma venda encontrada!',
                    'id' => 0
                ], 403);
            }
            $id_sale = $sale["id"];
            return $this->SendJson($response, [
                'status' => true,
                'msg' => 'Venda inserida com sucesso!',
                'id' => $id_sale
            ], 201);
        } catch (\Exception $e) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: ' . $e->getMessage(),
                'id' => 0
            ], 500);
        }
    }
    public function update($request, $response)
    {
        $form = $request->getParsedBody();
        $id = $form['id'] ?? null;
        $id_cliente = $form['id_cliente'] ?? null;
        $observacao = $form['observacao'] ?? null;
        if (is_null($id)) {
            return $this->SendJson($response, ['status' => false, 'msg' => 'Para alterar a venda informa o código!'], 403);
        }

        try {
            $total_venda = SelectQuery::select("sum(total_liquido) as total_liquido,sum(total_bruto) as total_bruto")
                ->from('item_sale')
                ->where('id_venda', '=', $id)
                ->fetch();

            $FieldAndValues = [
                'total_bruto' => $total_venda['total_bruto'],
                'total_liquido' => $total_venda['total_liquido']
            ];
            #Alteramos o código do cliente
            if (!is_null($id_cliente)) {
                $FieldAndValues['id_cliente'] = $id_cliente;
            }
            #Alteramos a observação
            if (!is_null($observacao)) {
                $FieldAndValues['observacao'] = $observacao;
            }
            $isUpdated = UpdateQuery::table('sale')->set($FieldAndValues)->update();
            if (!$isUpdated) {
                return $this->SendJson($response, ['status' => false, 'msg' => 'Restrição: ' . $isUpdated, 'id' => 0], 500);
            }
            return $this->SendJson($response, [
                'status' => true,
                'msg' => 'Atualização realizada com sucesso!',
                'id' => $id,
                'data' => $total_venda
            ]);
        } catch (\Exception $e) {
            return $this->SendJson($response, ['status' => false, 'msg' => 'Restrição: ' . $e->getMessage(), 'id' => 0], 500);
        }
    }
    public function alterar($request, $response, $args)
    {
        $id = $args['id'];
        try {
            $sale = SelectQuery::select()
                ->from('sale')
                ->where('id', '=', $id)
                ->fetch();
            if (!$sale) {
                return header('Location: /venda/lista');
                die;
            }
            $dadosTemplate = [
                'titulo' => 'Página inicial',
                'acao' => 'e',
                'id' => $id,
                'sale' => $sale
            ];
            return $this->getTwig()
                ->render($response, $this->setView('sale'), $dadosTemplate)
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
    public function insertitem($request, $response)
    {
        #captura os dados do formulário
        $form = $request->getParsedBody();
        $id = $form['id'] ?? null;
        $id_produto = $form['pesquisa'];
        #Verificar se o id da venda esta vasio ou nulo
        if (empty($id) or is_null($id)) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: O ID da venda é obrigatório!',
                'id' => 0
            ], 403);
        }
        #Verificar se o id do produto esta vasio ou nulo
        if (empty($id_produto) or is_null($id_produto)) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: O ID do produto é obrigatório!',
                'id' => 0
            ], 403);
        }
        try {
            #Selecionamos o produto que esta sendo vendido
            $produto = SelectQuery::select()->from('product')->where('id', '=', $id_produto)->fetch();
            if (!$produto) {
                return $this->SendJson($response, [
                    'status' => false,
                    'msg' => 'Restrição: Nenhum produto localizado!',
                    'id' => 0
                ], 403);
            }
            $FieldAndValue = [
                'id_venda' => $id,
                'id_produto' => $id_produto,
                'quantidade' => 1,
                'total_bruto' => $produto['valor'],
                'total_liquido' => $produto['valor'],
                'desconto' => 0,
                'acrescimo' => 0,
                'nome' => $produto['nome'],
            ];
            $isInserted = InsertQuery::table('item_sale')->save($FieldAndValue);
            if (!$isInserted) {
                return $this->SendJson($response, [
                    'status' => false,
                    'msg' => 'Restrição: ' . $isInserted,
                    'id' => 0
                ], 500);
            }
            #Atualiza o total da venda
            $sale = SelectQuery::select("COALESCE(SUM(total_bruto),0) AS total_bruto, COALESCE(SUM(total_liquido),0) AS total_liquido")
                ->from('item_sale')
                ->where('id_venda', '=', $id)
                ->fetch();

            UpdateQuery::table('sale')
                ->set([
                    'total_bruto' => $sale['total_bruto'],
                    'total_liquido' => $sale['total_liquido']
                ])
                ->where('id', '=', $id)
                ->update();
            return $this->SendJson($response, [
                'status' => true,
                'msg' => 'Item inserido com sucesso!',
                'id' => 0
            ]);
        } catch (\Exception $e) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: ' . $e->getMessage(),
                'id' => 0
            ], 500);
        }
    }
    public function listitemsale($request, $response)
    {
        $form = $request->getParsedBody();
        $id = $form['id'] ?? null;
        #Verificar se o id da venda esta vasio ou nulo
        if (empty($id) or is_null($id)) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: O ID da venda é obrigatório!',
                'id' => 0
            ], 403);
        }

        $total_venda = SelectQuery::select("sum(total_liquido) as total_liquido,sum(total_bruto) as total_bruto")
            ->from('item_sale')
            ->where('id_venda', '=', $id)
            ->fetch();

        $items = SelectQuery::select('id,nome,total_liquido')
            ->from('item_sale')
            ->where('id_venda', '=', $id)
            ->fetchAll();

        $data = [
            'status' => true,
            'id' => $id,
            'msg' => 'Dados listados com sucesso!',
            'sale' => $total_venda,
            'data' => $items
        ];
        return $this->SendJson($response, $data);
    }
    public function deleteitem($request, $response)
    {
        $form = $request->getParsedBody();
        $id_item = $form['id_item'] ?? null;
        $id_venda = $form['id'] ?? null;
        if (is_null($id_item)) {
            return $this->SendJson($response, ['status' => false, 'msg' => 'Por favor informe, o código do item para remover', 'id' => 0], 403);
        }
        if (is_null($id_venda)) {
            return $this->SendJson($response, ['status' => false, 'msg' => 'Por favor informe, o código do venda prosseguir', 'id' => 0], 403);
        }
        try {
            $IsDeleted = DeleteQuery::table('item_sale')->where('id', '=', $id_item)->delete();
            if (!$IsDeleted) {
                return $this->SendJson($response, ['status' => false, 'msg' => 'Não foi possível remover o item tente novamente mais tarde!', 'id' => $id_item], 403);
            }
            $sale = SelectQuery::select("COALESCE(SUM(total_bruto),0) AS total_bruto, COALESCE(SUM(total_liquido),0) AS total_liquido")
                ->from('item_sale')
                ->where('id_venda', '=', $id_venda)
                ->fetch();
            #Total de itens da venda
            $itens = SelectQuery::select('count(*) as qtd ')
                ->from('item_sale')
                ->where('id_venda', '=', $id_venda)
                ->fetch();
            $FieldAndValues = [
                'total_bruto' => $sale['total_bruto'],
                'total_liquido' => $sale['total_liquido']
            ];
            UpdateQuery::table('sale')->set($FieldAndValues)->where('id', '=', $id_venda)->update();
            return $this->SendJson($response, ['status' => true, 'msg' => 'Item removido com sucesso!', 'id' => $id_item, 'sale' => $sale, 'itens' => $itens['qtd']]);
        } catch (\Exception $e) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: ' . $e->getMessage(),
                'id' => 0
            ], 500);
        }
    }
    public function selectsaledata($request, $response)
    {
        $form = $request->getParsedBody();
        $id_venda = $form['id'] ?? null;
        if (is_null($id_venda)) {
            $data = [
                'status' => false,
                'msg' => 'Informe o código da venda',
                'id' => 0,
                'total_bruto' => 0.00,
                'total_liquido' => 0.00,
                'total_diferenca' => 0.00,
                'itens' => 0,
                'installment' => []
            ];
            return $this->SendJson($response, $data, 403);
        }

        $sale = SelectQuery::select()
            ->from('sale')
            ->where('id', '=', $id_venda)
            ->fetch();
        #Seleciona a quantidade de itens da venda
        $itens = SelectQuery::select('count(*) as qtd ')
            ->from('item_sale')
            ->where('id_venda', '=', $id_venda)
            ->fetch();

        $installment = SelectQuery::select()
            ->from('installment_sale')
            ->where('id_venda', '=', $id_venda)
            ->fetchAll();

        if (!$installment) {
            $data = [
                'status' => true,
                'msg' => 'Nenhuma parcela encontrada para esta venda',
                'id' => $id_venda,
                'total_bruto' => $sale['total_bruto'] ?? 0.00,
                'total_liquido' => $sale['total_liquido'] ?? 0.00,
                'total_diferenca' => $sale ? round(($sale['total_bruto'] ?? 0.00) - ($sale['total_liquido'] ?? 0.00), 2) : 0.00,
                'itens' => $itens['qtd'] ?? 0,
                'installment' => []
            ];
            return $this->SendJson($response, $data);
        }
        /**
         * Reduz o array flat em grupos por título de forma performática,
         * acumulando totais em uma única passagem O(n).
         */
        ['grouped' => $grouped, 'totalBruto' => $totalBruto, 'totalLiquido' => $totalLiquido] =
            array_reduce(
                $installment,
                static function (array $carry, array $item): array {
                    # Captura o total bruto (idêntico em todas as linhas)
                    $carry['totalBruto'] = $item['valor_total_venda'];
                    # Acumula o total líquido parcela a parcela
                    $carry['totalLiquido'] += $item['valor_parcela'];
                    # Remove 'titulo' do item filho e agrupa pela chave
                    $titulo = $item['titulo'];
                    $carry['grouped'][$titulo][] = array_diff_key($item, ['titulo' => null]);
                    return $carry;
                },
                ['grouped' => [], 'totalBruto' => 0.0, 'totalLiquido' => 0.0]
            );
        $installmentFormatted = array_map(
            static fn(array $items): array => count($items) === 1 ? $items[0] : $items,
            $grouped
        );
        $totalDiferenca = round($totalBruto - $totalLiquido, 2);
        $totalLiquido   = round($totalLiquido, 2);
        $this->SendJson([
            'total_bruto'     => $totalBruto,
            'total_liquido'   => $totalLiquido,
            'total_diferenca' => $totalDiferenca,
            'itens' => $itens['qtd'] ?? 0,
            'installment'     => [$installmentFormatted],
        ]);
    }
    public function listinstallments($request, $response)
    {
        $form = $request->getParsedBody();
        $condicaoPagamento = $form['condicaoPagamento'] ?? null;
        if (is_null($condicaoPagamento)) {
            return $this->SendJson($response, ['status' => false, 'msg' => 'Informe a condição de pagamento', 'id' => 0, 'data' => []], 403);
        }

        $installment = SelectQuery::select()
            ->from('installment')
            ->where('id_pagamento', '=', $condicaoPagamento)
            ->fetchAll();

        if (!$installment) {
            return $this->SendJson($response, ['status' => true, 'msg' => 'Nenhuma parcela encontrada para esta venda', 'id' => $condicaoPagamento, 'data' => []]);
        }
        return $this->SendJson($response, ['status' => true, 'msg' => 'Parcelas listadas com sucesso!', 'id' => $condicaoPagamento, 'data' => $installment]);
    }
}
