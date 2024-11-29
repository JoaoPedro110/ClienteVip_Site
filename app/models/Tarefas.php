<?php

class TarefaModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function cadastrarTarefa($dados)
    {
        $this->db->query('INSERT INTO tarefas (titulo, descricao, status, empresa_id, prioridade, data_termino) 
                          VALUES (:titulo, :descricao, :status, :empresa_id, :prioridade, :data_termino)');

        $this->db->bind(':titulo', $dados['titulo']);
        $this->db->bind(':descricao', $dados['descricao']);
        $this->db->bind(':status', $dados['status']);
        $this->db->bind(':empresa_id', $dados['empresa_id']);
        $this->db->bind(':prioridade', $dados['prioridade']);
        $this->db->bind(':data_termino', $dados['data_termino']);

        return $this->db->execute();
    }

    public function listarTarefas()
    {
        $this->db->query('SELECT id, titulo, descricao, status, empresa_id, prioridade, data_termino FROM tarefas');
        return $this->db->resultSet();
    }

    public function listarTarefaPorId($id)
    {
        $this->db->query('SELECT * FROM tarefas WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function editarTarefa($id, $dados)
    {
        $this->db->query('UPDATE tarefas SET titulo = :titulo, descricao = :descricao, status = :status, empresa_id = :empresa_id, 
                          prioridade = :prioridade, data_termino = :data_termino WHERE id = :id');

        $this->db->bind(':titulo', $dados['titulo']);
        $this->db->bind(':descricao', $dados['descricao']);
        $this->db->bind(':status', $dados['status']);
        $this->db->bind(':empresa_id', $dados['empresa_id']);
        $this->db->bind(':prioridade', $dados['prioridade']);
        $this->db->bind(':data_termino', $dados['data_termino']);
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    public function excluirTarefa($id)
    {
        $this->db->query('DELETE FROM tarefas WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
