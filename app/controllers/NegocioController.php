<?php

class NegocioController
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function cadastrar($dados)
    {
        return $this->model->cadastrarNegocio($dados);
    }

    public function listar()
    {
        return $this->model->listarNegocios();
    }

    public function buscarPorID($id)
    {
        return $this->model->buscarNegocioPorID($id);
    }

    public function atualizar($id, $dados)
    {
        try {
            // Chama o método de atualização no modelo e verifica o retorno
            if ($this->model->atualizarNegocio($id, $dados)) {
                return ['success' => true, 'message' => 'Negócio atualizado com sucesso.'];
            } else {
                return ['success' => false, 'message' => 'Erro ao atualizar negócio.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function excluir($id)
    {
        return $this->model->excluirNegocio($id);
    }
}
