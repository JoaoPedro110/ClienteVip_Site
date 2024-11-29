<?php

class EmpresaController
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function cadastrar($dados)
    {
        return $this->model->cadastrarEmpresa($dados);
    }

    public function listarEmpresas()
    {
        return $this->model->listarEmpresas();
    }
}
