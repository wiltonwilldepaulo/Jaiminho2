<?php

namespace app\controller;

use app\database\builder\SelectQuery;

class Product extends Base
{
    public function listproductdata($request, $response)
    {
        $form = $request->getParsedBody();
        $term = $form['term'] ?? null;
        $query = SelectQuery::select('id, codigo_barra, nome')->from('product');
        if ($term != null) {
            $query->where('codigo_barra', 'ILIKE', "%{$term}%", 'or')
                ->where('nome', 'ILIKE', "%{$term}%");
        }
        $data = [];
        $results = $query->fetchAll();
        foreach ($results as $key => $item) {
            $data['results'][$key] = [
                'id' => $item['id'],
                'text' => $item['nome'] . ' - Cód. barra: ' . $item['codigo_barra']
            ];
        }
        #$data['pagination'] = ['more' => true];
        return $this->SendJson($response, $data);
    }
}
