<?php

class EmpresaModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function cadastrarEmpresa($dados)
    {
        $this->db->query('INSERT INTO empresas (nome, setor, endereco, cidade, estado, cep, telefone, email) 
                          VALUES (:nome, :setor, :endereco, :cidade, :estado, :cep, :telefone, :email)');

        $this->db->bind(':nome', $dados['nome']);
        $this->db->bind(':setor', $dados['setor']);
        $this->db->bind(':endereco', $dados['endereco']);
        $this->db->bind(':cidade', $dados['cidade']);
        $this->db->bind(':estado', $dados['estado']);
        $this->db->bind(':cep', $dados['cep']);
        $this->db->bind(':telefone', $dados['telefone']);
        $this->db->bind(':email', $dados['email']);

        return $this->db->execute();
    }

    public function listarEmpresas()
    {
        $this->db->query('SELECT * FROM empresas');
        return $this->db->resultSet();
    }
}
