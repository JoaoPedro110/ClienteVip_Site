<?php


class ClienteModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function cadastrarCliente($dados)
    {
        try {
            $this->db->query('
                INSERT INTO cliente 
                (nome, telefone, email, idade, cpf, genero, senha, endereco_completo) 
                VALUES 
                (:nome, :telefone, :email, :idade, :cpf, :genero, :senha, :endereco_completo)
            ');

            $this->db->bind(':nome', $dados['nome']);
            $this->db->bind(':telefone', $dados['telefone']);
            $this->db->bind(':email', $dados['email']);
            $this->db->bind(':idade', $dados['idade']);
            $this->db->bind(':cpf', $dados['cpf']);
            $this->db->bind(':genero', $dados['genero']);
            $this->db->bind(':senha', password_hash($dados['senha'], PASSWORD_DEFAULT));
            $this->db->bind(':endereco_completo', $dados['endereco_completo']);

            $this->db->execute();
            return ['success' => true, 'message' => 'Cliente cadastrado com sucesso.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao cadastrar cliente: ' . $e->getMessage()];
        }
    }


    public function buscarClientePorCEP($cep)
    {
        $this->db->query('SELECT * FROM cliente WHERE logradouro = :logradouro');
        $this->db->bind(':logradouro', $cep);
        try {
            return $this->db->single();
        } catch (Exception $e) {
            throw new Exception('Erro ao buscar cliente por CEP: ' . $e->getMessage());
        }
    }

    // Função para verificar se o CPF já está cadastrado no banco de dados
    private function cpfDuplicado($cpf)
    {
        $this->db->query('SELECT cpf FROM cliente WHERE cpf = :cpf');
        $this->db->bind(':cpf', $cpf);

        try {
            $this->db->execute();
            $result = $this->db->single();
            return !empty($result); // Retorna true se o CPF já existir
        } catch (PDOException $e) {
            throw new Exception('Erro ao verificar duplicidade do CPF: ' . $e->getMessage());
        }
    }

    // Função de validação de CPF com cálculo de dígitos verificadores
    private function validarCPF($cpf)
    {
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    public function buscarClientePorCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11) {
            throw new Exception('CPF inválido. Deve conter exatamente 11 dígitos.');
        }

        if (!$this->validarCPF($cpf)) {
            throw new Exception('CPF inválido.');
        }

        $this->db->query('SELECT * FROM cliente WHERE cpf = :cpf');
        $this->db->bind(':cpf', $cpf);

        try {
            $cliente = $this->db->single();
            return $cliente ? $cliente : false;
        } catch (PDOException $e) {
            throw new Exception('Erro ao buscar cliente: ' . $e->getMessage());
        }
    }

    public function atualizarSenha($cpf, $novaSenhaHash)
    {
        $this->db->query('UPDATE cliente SET senha = :senha WHERE cpf = :cpf');
        $this->db->bind(':senha', $novaSenhaHash);
        $this->db->bind(':cpf', $cpf);

        try {
            $this->db->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception('Erro ao atualizar senha: ' . $e->getMessage());
        }
    }

    public function atualizarCliente($cpf, $dados)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11) {
            throw new Exception('CPF inválido. Deve conter exatamente 11 dígitos.');
        }

        if (!$this->validarCPF($cpf)) {
            throw new Exception('CPF inválido.');
        }

        $this->db->query('UPDATE cliente SET nome = :nome, telefone = :telefone, email = :email, 
                          endereco = :endereco, idade = :idade, genero = :genero WHERE cpf = :cpf');

        $this->db->bind(':nome', $dados['nome']);
        $this->db->bind(':telefone', $dados['telefone']);
        $this->db->bind(':email', $dados['email']);
        $this->db->bind(':endereco', $dados['endereco']);
        $this->db->bind(':idade', $dados['idade']);
        $this->db->bind(':genero', $dados['genero']);
        $this->db->bind(':cpf', $cpf);

        try {
            return $this->db->execute();
        } catch (Exception $e) {
            throw new Exception('Erro ao atualizar cliente: ' . $e->getMessage());
        }
    }

    public function verificarSenhaAtual($cpf, $senhaAtual)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        $this->db->query('SELECT senha FROM cliente WHERE cpf = :cpf');
        $this->db->bind(':cpf', $cpf);

        try {
            $cliente = $this->db->single();
            if ($cliente && isset($cliente['senha'])) {
                return password_verify($senhaAtual, $cliente['senha']);
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception('Erro ao verificar senha atual: ' . $e->getMessage());
        }
    }
}
