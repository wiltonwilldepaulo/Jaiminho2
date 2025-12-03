<?php

namespace app\controller;

use app\database\builder\InsertQuery;
use app\database\builder\SelectQuery;

class Login extends Base
{
    public function login($request, $response)
    {
        try {
            $dadosTemplate = [
                'titulo' => 'Autenticação'
            ];
            return $this->getTwig()
                ->render($response, $this->setView('login'), $dadosTemplate)
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
        }
    }
    public function precadastro($request, $response)
    {
        try {
            #Captura os dados do form
            $form = $request->getParsedBody();
            #Capturar os dados do usuário.
            $dadosUsuario = [
                'nome' => $form['nome'],
                'sobrenome' => $form['sobrenome'],
                'cpf' => $form['cpf'],
                'rg' => $form['rg'],
                'senha' => password_hash($form['senha'], PASSWORD_DEFAULT)
            ];
            $IsInseted = InsertQuery::table('usuario')->save($dadosUsuario);
            if (!$IsInseted) {
                return $this->SendJson(
                    $response,
                    ['status' => false, 'msg' => 'Restrição: ', $IsInseted, 'id' => 0],
                    403
                );
            }
            #Captura o código do ultimo usuário cadastrado na tabela de usuário
            $id = SelectQuery::select('id')->from('usuario')->order('id', 'desc')->fetch();
            #Colocamos o ID do ultimo usuário cadastrado na varaivel $id_usuario.
            $id_usuario = $id['id'];
            #Finalizar o pré-cadastro.
            #Cadastrar todos os contatos: E-mail, Celular, WhastaApp.
        } catch (\Exception $e) {
        }
    }
    public function autenticar($request, $response)
    {
        try {
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
        }
    }
}
