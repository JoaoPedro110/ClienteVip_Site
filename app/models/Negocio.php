<?php

class NegocioModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function cadastrarNegocio($dados)
    {
        $this->db->query('INSERT INTO negocios (data_negocio, valor_transacao, notas, status, titulo, descricao, empresa_id) 
                          VALUES (:data_negocio, :valor_transacao, :notas, :status, :titulo, :descricao, :empresa_id)');

        $this->db->bind(':data_negocio', $dados['data_negocio']);
        $this->db->bind(':valor_transacao', $dados['valor_transacao']);
        $this->db->bind(':notas', $dados['notas']);
        $this->db->bind(':status', $dados['status']);
        $this->db->bind(':titulo', $dados['titulo']);
        $this->db->bind(':descricao', $dados['descricao']);
        $this->db->bind(':empresa_id', $dados['empresa_id']);

        return $this->db->execute();
    }

    public function listarNegocios()
    {
        $this->db->query('
            SELECT negocios.*, empresas.nome AS empresa_nome 
            FROM negocios 
            JOIN empresas ON negocios.empresa_id = empresas.empresa_id
        ');
        return $this->db->resultSet();
    }

    public function buscarNegocioPorID($id_negocio)
    {
        $this->db->query('SELECT * FROM negocios WHERE id_negocio = :id_negocio');
        $this->db->bind(':id_negocio', $id_negocio);
        return $this->db->single();
    }

    public function atualizarNegocio($id_negocio, $dados)
    {
        $this->db->query('UPDATE negocios SET 
            data_negocio = :data_negocio, 
            valor_transacao = :valor_transacao, 
            notas = :notas,
            status = :status, 
            titulo = :titulo, 
            descricao = :descricao, 
            empresa_id = :empresa_id 
            WHERE id_negocio = :id_negocio');

        // Realiza o bind dos parâmetros
        $this->db->bind(':data_negocio', $dados['data_negocio']);
        $this->db->bind(':valor_transacao', $dados['valor_transacao']);
        $this->db->bind(':notas', $dados['notas']);
        $this->db->bind(':status', $dados['status']);
        $this->db->bind(':titulo', $dados['titulo']);
        $this->db->bind(':descricao', $dados['descricao']);
        $this->db->bind(':empresa_id', $dados['empresa_id']);
        $this->db->bind(':id_negocio', $id_negocio);

        return $this->db->execute();
    }


    public function excluirNegocio($id_negocio)
    {
        $this->db->query('DELETE FROM negocios WHERE id_negocio = :id_negocio');
        $this->db->bind(':id_negocio', $id_negocio);
        return $this->db->execute();
    }
}
