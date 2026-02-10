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
        $data['results'] = [];
        if ($term != null) {
        }
        $data['results'] = $query->fetchAll();
        return $this->SendJson($response, $data);
    }
}
