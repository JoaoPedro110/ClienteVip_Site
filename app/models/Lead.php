<?php

class LeadModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Função para cadastrar um novo lead
    public function cadastrarLead($dados)
    {
        // Monta a query para inserir o lead no banco de dados
        $this->db->query('INSERT INTO leads (nome, status, empresa, cargo, telefone, email, mensagem, data_criacao) 
                          VALUES (:nome, :status, :empresa, :cargo, :telefone, :email, :mensagem, :data_criacao)');

        // Bind dos parâmetros para cada campo, garantindo que todos estejam definidos
        $this->db->bind(':nome', $dados['nome']);
        $this->db->bind(':status', $dados['status']);
        $this->db->bind(':empresa', $dados['empresa']);
        $this->db->bind(':cargo', $dados['cargo']);
        $this->db->bind(':telefone', $dados['telefone']);
        $this->db->bind(':email', $dados['email']);
        $this->db->bind(':mensagem', $dados['mensagem']);
        $this->db->bind(':data_criacao', date('Y-m-d H:i:s'));  // Define a data atual para o campo data_criacao

        try {
            return $this->db->execute();
        } catch (Exception $e) {
            throw new Exception('Erro ao cadastrar lead: ' . $e->getMessage());
        }
    }


    // Função para buscar um lead pelo ID
    public function buscarLeadPorID($id)
    {
        $this->db->query('SELECT * FROM leads WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function atualizarLead($id, $dados)
    {
        $this->db->query('UPDATE leads SET nome = :nome, status = :status, empresa = :empresa, cargo = :cargo, telefone = :telefone, email = :email, mensagem = :mensagem
                      WHERE id = :id');
        $this->db->bind(':nome', $dados['nome']);
        $this->db->bind(':status', $dados['status']);
        $this->db->bind(':empresa', $dados['empresa']);
        $this->db->bind(':cargo', $dados['cargo']);
        $this->db->bind(':telefone', $dados['telefone']);
        $this->db->bind(':email', $dados['email']);
        $this->db->bind(':mensagem', $dados['mensagem']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function excluirLead($id)
    {
        $this->db->query('DELETE FROM leads WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }


    // Função para listar todos os leads
    public function listarLeads()
    {
        $this->db->query('SELECT * FROM leads');

        try {
            return $this->db->resultSet();
        } catch (Exception $e) {
            throw new Exception('Erro ao listar leads: ' . $e->getMessage());
        }
    }
}
