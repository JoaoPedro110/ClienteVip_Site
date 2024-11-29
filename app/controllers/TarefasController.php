<?php

class TarefaController
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function cadastrar($dados)
    {
        if ($this->isDataValida($dados['data_termino'])) {
            return $this->model->cadastrarTarefa($dados);
        } else {
            return false; // Retorna falso se a data não for válida
        }
    }

    public function listar()
    {
        return $this->model->listarTarefas();
    }

    public function editar($id, $dados)
    {
        if ($this->isDataValida($dados['data_termino'])) {
            return $this->model->editarTarefa($id, $dados);
        } else {
            return false;
        }
    }

    public function excluir($id)
    {
        return $this->model->excluirTarefa($id);
    }

    public function listarPorId($id)
    {
        return $this->model->listarTarefaPorId($id);
    }

    private function isDataValida($data)
    {
        $dataFormatoValido = DateTime::createFromFormat('Y-m-d', $data);
        if ($dataFormatoValido && $dataFormatoValido->format('Y-m-d') === $data) {
            $dataAtual = new DateTime();
            return $dataFormatoValido >= $dataAtual;
        }
        return false;
    }
}
