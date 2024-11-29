<?php

class LeadController
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }
    public function cadastrar($dados)
    {
        try {
            // Verifica se os dados obrigatórios estão presentes
            if (empty($dados['nome']) || empty($dados['status']) || empty($dados['email'])) {
                echo '<div id="error-message" class="alert alert-danger text-center mt-4">Nome, status e email são obrigatórios.</div>';
                echo '<script>setTimeout(function() { document.getElementById("error-message").style.display = "none"; }, 5000);</script>';
                return ['success' => false, 'message' => 'Nome, status e email são obrigatórios.'];
            }

            // Chama o método correto do modelo para cadastrar o lead
            if ($this->model->cadastrarLead($dados)) {
                echo '<div id="success-message" class="alert alert-success text-center mt-4">Lead cadastrado com sucesso.</div>';
                echo '<script>setTimeout(function() { document.getElementById("success-message").style.display = "none"; }, 5000);</script>';
                return ['success' => true, 'message' => 'Lead cadastrado com sucesso.'];
            } else {
                echo '<div id="error-message" class="alert alert-danger text-center mt-4">Erro ao cadastrar lead.</div>';
                echo '<script>setTimeout(function() { document.getElementById("error-message").style.display = "none"; }, 5000);</script>';
                return ['success' => false, 'message' => 'Erro ao cadastrar lead.'];
            }
        } catch (Exception $e) {
            echo '<div id="error-message" class="alert alert-danger text-center mt-4">' . $e->getMessage() . '</div>';
            echo '<script>setTimeout(function() { document.getElementById("error-message").style.display = "none"; }, 5000);</script>';
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function buscarPorID($id)
    {
        try {
            return $this->model->buscarLeadPorID($id);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao buscar lead: ' . $e->getMessage()];
        }
    }

    public function atualizar($id, $dados)
    {
        try {
            if ($this->model->atualizarLead($id, $dados)) {
                return ['success' => true, 'message' => 'Lead atualizado com sucesso.'];
            } else {
                return ['success' => false, 'message' => 'Erro ao atualizar lead.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function excluir($id)
    {
        try {
            if ($this->model->excluirLead($id)) {
                return ['success' => true, 'message' => 'Lead excluído com sucesso.'];
            } else {
                return ['success' => false, 'message' => 'Erro ao excluir lead.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function listar()
    {
        try {
            return $this->model->listarLeads();
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
